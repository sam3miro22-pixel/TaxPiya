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
let heartbeatTimer = null;

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

function startHeartbeat() {
    if (heartbeatTimer) clearInterval(heartbeatTimer);
    heartbeatTimer = setInterval(async () => {
        if (connectionStatus !== 'connected' || !sock) return;
        try {
            await sock.sendPresenceUpdate('available');
        } catch (e) {
            console.warn('[WhatsApp] Heartbeat failed:', e.message);
        }
    }, 4 * 60 * 1000);
}

function stopHeartbeat() {
    if (heartbeatTimer) {
        clearInterval(heartbeatTimer);
        heartbeatTimer = null;
    }
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
            keepAliveIntervalMs: 20000,
            retryRequestDelayMs: 500,
            browser: ['Taxpiya', 'Chrome', '125.0.0'],
            syncFullHistory: false,
            markOnlineOnConnect: true,
        });

        sock.ev.on('creds.update', async (creds) => {
            await saveCreds(creds);
            // Backup immediately on credential update (login/re-auth)
            await backupSessionToGithub();
        });

        // ── Anti-ban state ────────────────────────────────────────────────────
        // Per-user processing queue to avoid race conditions and flooding
        const userQueues   = new Map(); // jid -> Promise (current processing chain)
        const lastReplied  = new Map(); // jid -> timestamp (rate limit: 1 reply per 10s)
        const lastMsgHash  = new Map(); // jid -> {hash, ts} (dedup: 15s window)

        function msgHash(text) {
            // Simple hash for deduplication
            let h = 0;
            for (let i = 0; i < text.length; i++) { h = (Math.imul(31, h) + text.charCodeAt(i)) | 0; }
            return h;
        }

        function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

        function humanReadDelay()    { return 1000 + Math.random() * 1500; }   // 1 – 2.5 s
        function humanPrepareDelay() { return 500  + Math.random() * 1000; }   // 0.5 – 1.5 s
        function humanTypingDelay(text) {
            // 20 ms per character, clamped [3000, 8000]
            return Math.min(8000, Math.max(3000, text.length * 20));
        }

        async function processMessage(from, text, msgKey) {
            const now = Date.now();

            // 1) Deduplication: ignore identical message within 15 seconds
            const prev = lastMsgHash.get(from);
            const hash = msgHash(text.trim().toLowerCase());
            if (prev && prev.hash === hash && (now - prev.ts) < 15000) {
                console.log(`[WhatsApp AI] Dedup – skipping duplicate from ${from}`);
                return;
            }
            lastMsgHash.set(from, { hash, ts: now });

            // 2) Rate limit: at most one reply per 10 seconds per user
            const lastTs = lastReplied.get(from) || 0;
            if ((now - lastTs) < 10000) {
                console.log(`[WhatsApp AI] Rate-limit – skipping reply to ${from}`);
                return;
            }

            // 3) Human-like: wait before marking as read
            await sleep(humanReadDelay());
            if (!sock) return;
            try { await sock.readMessages([msgKey]); } catch {}

            // 4) Human-like: brief pause before showing typing
            await sleep(humanPrepareDelay());
            if (!sock) return;
            try { await sock.sendPresenceUpdate('composing', from); } catch {}

            // 5) Call Groq
            const reply = await askGroq(text);

            // 6) Human-like: simulate typing duration based on reply length
            const typingMs = reply ? humanTypingDelay(reply) : 2000;
            await sleep(typingMs);
            if (!sock) return;
            try { await sock.sendPresenceUpdate('paused', from); } catch {}

            if (!reply) return;

            // 7) Update rate-limit timestamp BEFORE sending
            lastReplied.set(from, Date.now());

            await sock.sendMessage(from, { text: reply });
            console.log(`[WhatsApp AI] Replied to ${from} (${reply.length} chars)`);
        }

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

                // Chain onto the per-user queue so messages are processed
                // sequentially for each sender (prevents race conditions)
                const prev = userQueues.get(from) || Promise.resolve();
                const next = prev.then(() => processMessage(from, text, msg.key)).catch(() => {});
                userQueues.set(from, next);
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
                connectionStatus = 'disconnected';
                stopHeartbeat();
                const code = lastDisconnect?.error?.output?.statusCode;
                const loggedOut = code === DisconnectReason.loggedOut;
                const restartRequired = code === DisconnectReason.restartRequired;
                console.log(`[WhatsApp] Connection closed (code=${code}). LoggedOut=${loggedOut}`);

                if (loggedOut) {
                    try { fs.rmSync(sessionDir, { recursive: true, force: true }); } catch {}
                    try { fs.unlinkSync(qrFilePath); } catch {}
                    reconnectDelay = 4000;
                    console.log('[WhatsApp Session] Session invalidated. Clearing GitHub backup...');
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
                } else {
                    reconnectDelay = restartRequired ? 3000 : 5000;
                }

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
                startHeartbeat();
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
