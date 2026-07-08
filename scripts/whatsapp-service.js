const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const express = require('express');
const qrcode = require('qrcode');
const fs = require('fs');
const pino = require('pino');
const {
    sessionDir,
    qrFilePath,
    ensureDirs,
    env,
    backupSessionToGithub,
    restoreSessionFromGithub,
    clearGithubSessionBackup,
    clearLocalSession,
} = require('./whatsapp-session-store');

const app = express();
app.use(express.json());

ensureDirs();

let sock = null;
let connectionStatus = 'disconnected';
let currentQr = null;
let myNumber = null;
let reconnectDelay = 3000;
let retryTimer = null;
let sessionBackupTimer = null;
let heartbeatTimer = null;
let watchDebounce = null;
let isStarting = false;
let reconnectAttempts = 0;

function sleep(ms) {
    return new Promise(r => setTimeout(r, ms));
}

function shouldClearSession(code) {
    return code === DisconnectReason.loggedOut || code === DisconnectReason.badSession;
}

function nextReconnectDelay(code) {
    if (code === DisconnectReason.restartRequired) return 2000;
    if (code === DisconnectReason.timedOut) return 4000;
    reconnectAttempts += 1;
    return Math.min(30000, 3000 + reconnectAttempts * 2000);
}

function scheduleReconnect(delayMs, reason) {
    if (retryTimer) clearTimeout(retryTimer);
    console.log(`[WhatsApp] Reconnecting in ${Math.round(delayMs / 1000)}s (${reason})...`);
    retryTimer = setTimeout(() => startWhatsApp(), delayMs);
}

function scheduleSessionBackup() {
    if (sessionBackupTimer) clearInterval(sessionBackupTimer);
    sessionBackupTimer = setInterval(async () => {
        if (connectionStatus === 'connected') {
            await backupSessionToGithub();
        }
    }, 60 * 1000);
}

function watchSessionFiles() {
    try {
        fs.watch(sessionDir, { persistent: false }, () => {
            if (watchDebounce) clearTimeout(watchDebounce);
            watchDebounce = setTimeout(() => {
                if (connectionStatus === 'connected') {
                    backupSessionToGithub().catch(() => {});
                }
            }, 5000);
        });
    } catch (e) {
        console.warn('[WhatsApp Session] fs.watch unavailable:', e.message);
    }
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
    }, 2 * 60 * 1000);
}

function stopHeartbeat() {
    if (heartbeatTimer) {
        clearInterval(heartbeatTimer);
        heartbeatTimer = null;
    }
}

async function askGroq(userMessage) {
    const apiKey = env('GROQ_API_KEY');
    if (!apiKey) {
        console.warn('[WhatsApp AI] No GROQ_API_KEY');
        return null;
    }
    try {
        const res = await fetch('https://api.groq.com/openai/v1/chat/completions', {
            method: 'POST',
            headers: { Authorization: `Bearer ${apiKey}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                model: env('GROQ_MODEL', 'llama-3.1-8b-instant'),
                messages: [
                    {
                        role: 'system',
                        content: 'Eres TaxPiya Assistant, el chatbot oficial de la app de taxis TaxPiya en Colombia. Responde en español, muy conciso y servicial (máximo 4 oraciones). Ayuda con: cómo pedir viajes en el mapa, tarifas por distancia, recarga de billetera (Nequi), código de llegada, estado del viaje y soporte técnico.',
                    },
                    { role: 'user', content: userMessage },
                ],
                temperature: 0.35,
                max_tokens: 350,
            }),
        });
        if (!res.ok) {
            console.error('[WhatsApp AI] Groq status:', res.status);
            return null;
        }
        const data = await res.json();
        return data.choices?.[0]?.message?.content?.trim() || null;
    } catch (e) {
        console.error('[WhatsApp AI] Groq error:', e.message);
        return null;
    }
}

async function startWhatsApp() {
    if (isStarting) return;
    isStarting = true;

    if (retryTimer) {
        clearTimeout(retryTimer);
        retryTimer = null;
    }

    connectionStatus = 'connecting';

    try {
        if (sock) {
            try { sock.ev.removeAllListeners(); } catch (_) { /* ignore */ }
            try { sock.ws?.close(); } catch (_) { /* ignore */ }
            sock = null;
        }

        const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
        const { version } = await fetchLatestBaileysVersion();
        const logger = pino({ level: 'silent' });

        sock = makeWASocket({
            version,
            auth: state,
            logger,
            printQRInTerminal: false,
            connectTimeoutMs: 90000,
            defaultQueryTimeoutMs: 60000,
            keepAliveIntervalMs: 15000,
            retryRequestDelayMs: 500,
            browser: ['Taxpiya', 'Chrome', '125.0.0'],
            syncFullHistory: false,
            markOnlineOnConnect: true,
            generateHighQualityLinkPreview: false,
            shouldIgnoreJid: jid => jid.endsWith('@broadcast'),
            getMessage: async () => undefined,
        });

        sock.ev.on('creds.update', async () => {
            await saveCreds();
            await backupSessionToGithub();
        });

        const userQueues = new Map();
        const lastReplied = new Map();
        const lastMsgHash = new Map();

        function msgHash(text) {
            let h = 0;
            for (let i = 0; i < text.length; i++) {
                h = (Math.imul(31, h) + text.charCodeAt(i)) | 0;
            }
            return h;
        }

        function humanReadDelay() { return 1000 + Math.random() * 1500; }
        function humanPrepareDelay() { return 500 + Math.random() * 1000; }
        function humanTypingDelay(text) {
            return Math.min(8000, Math.max(3000, text.length * 20));
        }

        async function processMessage(from, text, msgKey) {
            const now = Date.now();
            const prev = lastMsgHash.get(from);
            const hash = msgHash(text.trim().toLowerCase());
            if (prev && prev.hash === hash && (now - prev.ts) < 15000) return;
            lastMsgHash.set(from, { hash, ts: now });

            const lastTs = lastReplied.get(from) || 0;
            if ((now - lastTs) < 10000) return;

            await sleep(humanReadDelay());
            if (!sock) return;
            try { await sock.readMessages([msgKey]); } catch (_) { /* ignore */ }

            await sleep(humanPrepareDelay());
            if (!sock) return;
            try { await sock.sendPresenceUpdate('composing', from); } catch (_) { /* ignore */ }

            const reply = await askGroq(text);
            await sleep(reply ? humanTypingDelay(reply) : 2000);
            if (!sock) return;
            try { await sock.sendPresenceUpdate('paused', from); } catch (_) { /* ignore */ }
            if (!reply) return;

            lastReplied.set(from, Date.now());
            await sock.sendMessage(from, { text: reply });
            console.log(`[WhatsApp AI] Replied to ${from}`);
        }

        sock.ev.on('messages.upsert', async (m) => {
            if (m.type !== 'notify') return;
            for (const msg of m.messages) {
                if (msg.key.fromMe) continue;
                const from = msg.key.remoteJid;
                if (!from || from.endsWith('@g.us')) continue;

                const text = msg.message?.conversation || msg.message?.extendedTextMessage?.text;
                if (!text || text.trim() === '') continue;

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
                reconnectAttempts = 0;
                try { fs.writeFileSync(qrFilePath, qr); } catch (_) { /* ignore */ }
                console.log('[WhatsApp] QR code ready. Waiting for scan...');
            }

            if (connection === 'close') {
                currentQr = null;
                const oldSock = sock;
                sock = null;
                connectionStatus = 'disconnected';
                stopHeartbeat();

                const code = lastDisconnect?.error?.output?.statusCode;
                const loggedOut = shouldClearSession(code);
                console.log(`[WhatsApp] Connection closed (code=${code}).`);

                if (loggedOut) {
                    clearLocalSession();
                    reconnectAttempts = 0;
                    reconnectDelay = 4000;
                    await clearGithubSessionBackup();
                } else if (code === DisconnectReason.connectionReplaced) {
                    console.warn('[WhatsApp] Otra instancia tomó la sesión — esperando y reconectando.');
                    reconnectDelay = 8000;
                } else {
                    reconnectDelay = nextReconnectDelay(code);
                }

                try { oldSock?.ev?.removeAllListeners(); } catch (_) { /* ignore */ }
                scheduleReconnect(reconnectDelay, `code=${code ?? 'unknown'}`);

            } else if (connection === 'open') {
                connectionStatus = 'connected';
                currentQr = null;
                reconnectAttempts = 0;
                reconnectDelay = 3000;
                try { fs.unlinkSync(qrFilePath); } catch (_) { /* ignore */ }
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
        sock = null;
        reconnectDelay = nextReconnectDelay(null);
        scheduleReconnect(reconnectDelay, 'startup-error');
    } finally {
        isStarting = false;
    }
}

async function bootstrap() {
    console.log('[WhatsApp] Bootstrapping service...');
    console.log('[WhatsApp] GitHub token:', env('GITHUB_BACKUP_TOKEN') ? 'present' : 'MISSING');

    await restoreSessionFromGithub();
    scheduleSessionBackup();
    watchSessionFiles();
    await startWhatsApp();
}

bootstrap();

app.get('/status', async (req, res) => {
    let qrImage = null;
    if (currentQr) {
        try { qrImage = await qrcode.toDataURL(currentQr); } catch (_) { /* ignore */ }
    }
    res.json({
        ok: true,
        status: connectionStatus,
        qr: currentQr,
        qrImage,
        user: myNumber,
        reconnectAttempts,
        sessionFiles: fs.existsSync(sessionDir) ? fs.readdirSync(sessionDir).length : 0,
        githubBackup: !!env('GITHUB_BACKUP_TOKEN'),
    });
});

app.get('/health', (req, res) => {
    res.json({ ok: connectionStatus === 'connected', status: connectionStatus });
});

app.post('/send', async (req, res) => {
    try {
        const { phone, message } = req.body;
        if (!phone || !message) {
            return res.status(400).json({ ok: false, message: 'Phone and message are required' });
        }
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
            try { await sock.logout(); } catch (_) { /* ignore */ }
            sock = null;
        }
        clearLocalSession();
        await clearGithubSessionBackup();
        reconnectAttempts = 0;
        reconnectDelay = 4000;
        retryTimer = setTimeout(startWhatsApp, 2000);
        res.json({ ok: true, message: 'Logged out. Generating new QR...' });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

app.post('/reconnect', async (req, res) => {
    try {
        if (connectionStatus === 'connected') {
            return res.json({ ok: true, message: 'Already connected' });
        }
        await startWhatsApp();
        res.json({ ok: true, message: 'Reconnect triggered', status: connectionStatus });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

const port = process.env.WHATSAPP_PORT || 8051;
app.listen(port, '127.0.0.1', () => {
    console.log(`[WhatsApp] Baileys service listening on port ${port} (localhost only)`);
});
