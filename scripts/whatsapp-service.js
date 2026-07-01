const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const express = require('express');
const qrcode = require('qrcode');
const fs = require('fs');
const path = require('path');
const pino = require('pino');
const https = require('https');

const app = express();
app.use(express.json());

const sessionDir = path.join(__dirname, '../storage/app/whatsapp-session');
const qrFilePath = path.join(__dirname, '../storage/app/whatsapp-qr.txt');

// Ensure storage directories exist
[path.join(__dirname, '../storage'), path.join(__dirname, '../storage/app'), sessionDir].forEach(dir => {
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
});

let sock = null;
let connectionStatus = 'disconnected';
let currentQr = null;
let myNumber = null;
let reconnectDelay = 4000;
let retryTimer = null;
let sessionBackupTimer = null;

// ─── ENV helpers ────────────────────────────────────────────────────────────
let _envCache = null;
function loadEnv() {
    if (_envCache) return _envCache;
    _envCache = {};
    try {
        const envPath = path.join(__dirname, '../.env');
        if (fs.existsSync(envPath)) {
            fs.readFileSync(envPath, 'utf8').split('\n').forEach(line => {
                const m = line.match(/^([^#=]+)=(.*)$/);
                if (m) _envCache[m[1].trim()] = m[2].trim().replace(/^["']|["']$/g, '');
            });
        }
    } catch (e) { /* ignore */ }
    // overlay actual process env (Render sets them at runtime)
    Object.assign(_envCache, process.env);
    return _envCache;
}

function env(key, fallback = '') {
    return (loadEnv()[key] ?? process.env[key] ?? fallback).toString().trim();
}

// ─── GROQ ───────────────────────────────────────────────────────────────────
async function askGroq(userMessage) {
    const apiKey = env('GROQ_API_KEY');
    if (!apiKey) { console.warn('[WhatsApp AI] No GROQ_API_KEY'); return null; }
    try {
        const res = await fetch('https://api.groq.com/openai/v1/chat/completions', {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${apiKey}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                model: 'llama-3.1-8b-instant',
                messages: [
                    {
                        role: 'system',
                        content: 'Eres TaxPiya Assistant, el chatbot oficial de la app de taxis TaxPiya en Colombia. Responde en español, muy conciso y servicial (máximo 4 oraciones). Ayuda con: cómo pedir viajes en el mapa, tarifas por distancia, recarga de billetera (Nequi), código de llegada, estado del viaje y soporte técnico. Si preguntan precio sin destino específico, indícales que lo calculen en la app antes de confirmar.'
                    },
                    { role: 'user', content: userMessage }
                ],
                temperature: 0.35,
                max_tokens: 350
            })
        });
        if (!res.ok) { console.error('[WhatsApp AI] Groq status:', res.status); return null; }
        const data = await res.json();
        return data.choices?.[0]?.message?.content?.trim() || null;
    } catch (e) {
        console.error('[WhatsApp AI] Groq error:', e.message);
        return null;
    }
}

// ─── GITHUB SESSION BACKUP ──────────────────────────────────────────────────
function githubHeaders() {
    return {
        'Authorization': `Bearer ${env('GITHUB_BACKUP_TOKEN')}`,
        'Accept': 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
        'User-Agent': 'TaxPiya-WhatsApp-Session',
        'Content-Type': 'application/json'
    };
}

function sessionContentsUrl() {
    const owner = env('GITHUB_BACKUP_OWNER', 'sam3miro22-pixel');
    const repo  = env('GITHUB_BACKUP_REPO',  'taxpiya-db-backup');
    return `https://api.github.com/repos/${owner}/${repo}/contents/whatsapp-session.tar.gz.b64`;
}

async function githubRequest(method, url, body) {
    return new Promise((resolve, reject) => {
        const u = new URL(url);
        const opts = {
            hostname: u.hostname,
            path: u.pathname + u.search,
            method,
            headers: githubHeaders()
        };
        const req = https.request(opts, res => {
            let data = '';
            res.on('data', c => data += c);
            res.on('end', () => resolve({ status: res.statusCode, body: data }));
        });
        req.on('error', reject);
        if (body) req.write(typeof body === 'string' ? body : JSON.stringify(body));
        req.end();
    });
}

async function backupSessionToGithub() {
    const token = env('GITHUB_BACKUP_TOKEN');
    if (!token) return;

    try {
        // Tar all session files into a single buffer
        const files = fs.readdirSync(sessionDir);
        if (files.length === 0) return;

        // Pack files as a JSON map: { filename: base64content }
        const sessionMap = {};
        for (const f of files) {
            const fp = path.join(sessionDir, f);
            if (fs.statSync(fp).isFile()) {
                sessionMap[f] = fs.readFileSync(fp).toString('base64');
            }
        }
        const packed = Buffer.from(JSON.stringify(sessionMap)).toString('base64');

        // Get current SHA if file exists
        let sha = null;
        const getMeta = await githubRequest('GET', sessionContentsUrl(), null);
        if (getMeta.status === 200) {
            try { sha = JSON.parse(getMeta.body).sha; } catch {}
        }

        const payload = {
            message: `WhatsApp session backup ${new Date().toISOString()}`,
            content: packed
        };
        if (sha) payload.sha = sha;

        const put = await githubRequest('PUT', sessionContentsUrl(), payload);
        if (put.status < 300) {
            console.log('[WhatsApp Session] Backed up to GitHub successfully.');
        } else {
            console.error('[WhatsApp Session] GitHub backup failed:', put.status, put.body.slice(0, 200));
        }
    } catch (e) {
        console.error('[WhatsApp Session] Backup error:', e.message);
    }
}

async function restoreSessionFromGithub() {
    const token = env('GITHUB_BACKUP_TOKEN');
    if (!token) {
        console.log('[WhatsApp Session] No GitHub token, skipping restore.');
        return false;
    }

    try {
        const getMeta = await githubRequest('GET', sessionContentsUrl(), null);
        if (getMeta.status === 404) {
            console.log('[WhatsApp Session] No backup found on GitHub (first run).');
            return false;
        }
        if (getMeta.status >= 300) {
            console.error('[WhatsApp Session] GitHub restore GET failed:', getMeta.status);
            return false;
        }

        const meta = JSON.parse(getMeta.body);
        const packed = Buffer.from(meta.content.replace(/\s/g, ''), 'base64').toString('utf8');
        const sessionMap = JSON.parse(packed);

        // Wipe existing session dir and restore files
        fs.rmSync(sessionDir, { recursive: true, force: true });
        fs.mkdirSync(sessionDir, { recursive: true });

        for (const [filename, b64] of Object.entries(sessionMap)) {
            fs.writeFileSync(path.join(sessionDir, filename), Buffer.from(b64, 'base64'));
        }

        console.log(`[WhatsApp Session] Restored ${Object.keys(sessionMap).length} files from GitHub.`);
        return true;
    } catch (e) {
        console.error('[WhatsApp Session] Restore error:', e.message);
        return false;
    }
}

// Schedule periodic session backup every 3 minutes while connected
function scheduleSessionBackup() {
    if (sessionBackupTimer) clearInterval(sessionBackupTimer);
    sessionBackupTimer = setInterval(async () => {
        if (connectionStatus === 'connected') {
            await backupSessionToGithub();
        }
    }, 3 * 60 * 1000);
}

// ─── WHATSAPP CONNECTION ─────────────────────────────────────────────────────
async function startWhatsApp() {
    if (retryTimer) { clearTimeout(retryTimer); retryTimer = null; }
    connectionStatus = 'connecting';

    try {
        const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
        const { version } = await fetchLatestBaileysVersion();
        const logger = pino({ level: 'silent' });

        sock = makeWASocket({
            version,
            auth: state,
            logger,
            printQRInTerminal: false,
            connectTimeoutMs: 60000,
            keepAliveIntervalMs: 25000,
            retryRequestDelayMs: 500,
            browser: ['Taxpiya', 'Chrome', '125.0.0'],
        });

        sock.ev.on('creds.update', async (creds) => {
            await saveCreds(creds);
            // Backup immediately on credential update (login/re-auth)
            await backupSessionToGithub();
        });

        // ── Incoming message auto-responder ──────────────────────────────────
        sock.ev.on('messages.upsert', async (m) => {
            if (m.type !== 'notify') return;
            for (const msg of m.messages) {
                if (msg.key.fromMe) continue;
                const from = msg.key.remoteJid;
                if (!from || from.endsWith('@g.us')) continue;

                const text = msg.message?.conversation ||
                             msg.message?.extendedTextMessage?.text;
                if (!text || text.trim() === '') continue;

                console.log(`[WhatsApp AI] Msg from ${from}: "${text.slice(0,80)}"`);

                try { await sock.readMessages([msg.key]); } catch {}
                try { await sock.sendPresenceUpdate('composing', from); } catch {}

                const reply = await askGroq(text);

                try { await sock.sendPresenceUpdate('paused', from); } catch {}

                if (reply) {
                    await sock.sendMessage(from, { text: reply });
                    console.log(`[WhatsApp AI] Replied to ${from}`);
                }
            }
        });

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                connectionStatus = 'qr';
                currentQr = qr;
                reconnectDelay = 4000;
                try { fs.writeFileSync(qrFilePath, qr); } catch {}
                console.log('[WhatsApp] QR code ready. Waiting for scan...');
            }

            if (connection === 'close') {
                currentQr = null;
                sock = null;
                const code = lastDisconnect?.error?.output?.statusCode;
                const loggedOut = code === DisconnectReason.loggedOut;
                console.log(`[WhatsApp] Connection closed (code=${code}). LoggedOut=${loggedOut}`);
                connectionStatus = 'disconnected';

                if (loggedOut) {
                    // Session invalidated — wipe local + GitHub backup so fresh QR is generated
                    try { fs.rmSync(sessionDir, { recursive: true, force: true }); } catch {}
                    try { fs.unlinkSync(qrFilePath); } catch {}
                    reconnectDelay = 4000;
                    console.log('[WhatsApp Session] Session invalidated. Clearing GitHub backup...');
                    // Clear GitHub backup by uploading empty marker
                    try {
                        const getMeta = await githubRequest('GET', sessionContentsUrl(), null);
                        if (getMeta.status === 200) {
                            const sha = JSON.parse(getMeta.body).sha;
                            await githubRequest('DELETE', sessionContentsUrl(), {
                                message: 'WhatsApp session cleared (logged out)',
                                sha
                            });
                        }
                    } catch {}
                }

                reconnectDelay = Math.min(reconnectDelay * 1.5, 60000);
                console.log(`[WhatsApp] Reconnecting in ${Math.round(reconnectDelay / 1000)}s...`);
                retryTimer = setTimeout(startWhatsApp, reconnectDelay);

            } else if (connection === 'open') {
                connectionStatus = 'connected';
                currentQr = null;
                reconnectDelay = 4000;
                try { fs.unlinkSync(qrFilePath); } catch {}
                const user = sock.user;
                myNumber = user?.id?.split(':')[0] ?? 'unknown';
                console.log(`[WhatsApp] Connected as: ${myNumber}`);
                // Backup session right after connecting
                await backupSessionToGithub();
            }
        });

    } catch (err) {
        console.error('[WhatsApp] Fatal startup error:', err.message);
        connectionStatus = 'disconnected';
        reconnectDelay = Math.min(reconnectDelay * 1.5, 60000);
        retryTimer = setTimeout(startWhatsApp, reconnectDelay);
    }
}

// ─── BOOTSTRAP ───────────────────────────────────────────────────────────────
async function bootstrap() {
    console.log('[WhatsApp] Attempting to restore session from GitHub...');
    await restoreSessionFromGithub();
    scheduleSessionBackup();
    await startWhatsApp();
}

bootstrap();

// ─── API ENDPOINTS ────────────────────────────────────────────────────────────
app.get('/status', async (req, res) => {
    let qrImage = null;
    if (currentQr) {
        try { qrImage = await qrcode.toDataURL(currentQr); } catch {}
    }
    res.json({ ok: true, status: connectionStatus, qr: currentQr, qrImage, user: myNumber });
});

app.post('/send', async (req, res) => {
    try {
        const { phone, message } = req.body;
        if (!phone || !message) return res.status(400).json({ ok: false, message: 'Phone and message are required' });
        if (connectionStatus !== 'connected' || !sock) {
            return res.status(503).json({ ok: false, message: 'WhatsApp not connected (status: ' + connectionStatus + ')' });
        }
        const jid = phone.replace(/\D/g, '') + '@s.whatsapp.net';
        await sock.sendMessage(jid, { text: message });
        res.json({ ok: true, message: 'Sent' });
    } catch (e) {
        console.error('[WhatsApp] Send error:', e.message);
        res.status(500).json({ ok: false, error: e.message });
    }
});

app.post('/logout', async (req, res) => {
    try {
        currentQr = null;
        myNumber = null;
        connectionStatus = 'disconnected';
        if (retryTimer) { clearTimeout(retryTimer); retryTimer = null; }
        if (sock) {
            try { await sock.logout(); } catch {}
            sock = null;
        }
        try { fs.rmSync(sessionDir, { recursive: true, force: true }); } catch {}
        try { fs.unlinkSync(qrFilePath); } catch {}
        reconnectDelay = 4000;
        retryTimer = setTimeout(startWhatsApp, 2000);
        res.json({ ok: true, message: 'Logged out. Generating new QR...' });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

const port = process.env.WHATSAPP_PORT || 8051;
app.listen(port, '127.0.0.1', () => {
    console.log(`[WhatsApp] Baileys service listening on port ${port} (localhost only)`);
});
