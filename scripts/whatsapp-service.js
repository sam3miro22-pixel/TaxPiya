const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion, makeCacheableSignalKeyStore } = require('@whiskeysockets/baileys');
const express = require('express');
const qrcode = require('qrcode');
const fs = require('fs');
const path = require('path');
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

const CONFIG = {
    KEEP_ALIVE_MS: 60000,
    HEARTBEAT_MS: 8 * 60 * 1000,
    WATCHDOG_MS: 45000,
    BACKUP_MS: 45 * 1000,
    RECONNECT_BASE_MS: 2500,
    RECONNECT_MAX_MS: 20000,
    RATE_LIMIT_MS: 6000,
    DEAF_SESSION_MS: 12 * 60 * 1000,
    PROCESS_RESTART_MS: 4 * 60 * 60 * 1000,
};

const app = express();
app.use(express.json());
ensureDirs();

let sock = null;
let connectionStatus = 'disconnected';
let currentQr = null;
let myNumber = null;
let lastDisconnectCode = null;
let lastConnectedAt = 0;
let lastDisconnectedAt = Date.now();
let lastMessageAt = 0;
let lastEventAt = 0;
let lastHeartbeatAt = 0;
let processStartedAt = Date.now();
let reconnectAttempts = 0;
let retryTimer = null;
let sessionBackupTimer = null;
let heartbeatTimer = null;
let watchdogTimer = null;
let watchDebounce = null;
let isStarting = false;
const lockFile = path.join(sessionDir, '.service.lock');

function sleep(ms) {
    return new Promise(r => setTimeout(r, ms));
}

function pathExists(p) {
    try { return fs.existsSync(p); } catch (_) { return false; }
}

function acquireServiceLock() {
    ensureDirs();
    if (pathExists(lockFile)) {
        try {
            const pid = parseInt(fs.readFileSync(lockFile, 'utf8'), 10);
            if (pid && pid !== process.pid) {
                try { process.kill(pid, 0); return false; } catch (_) { /* stale */ }
            }
        } catch (_) { /* ignore */ }
    }
    fs.writeFileSync(lockFile, String(process.pid));
    return true;
}

function releaseServiceLock() {
    try { if (pathExists(lockFile)) fs.unlinkSync(lockFile); } catch (_) { /* ignore */ }
}

process.on('exit', releaseServiceLock);
process.on('SIGTERM', () => { releaseServiceLock(); process.exit(0); });
process.on('SIGINT', () => { releaseServiceLock(); process.exit(0); });

function disconnectCode(lastDisconnect) {
    return lastDisconnect?.error?.output?.statusCode ?? null;
}

function shouldClearSession(code) {
    return code === DisconnectReason.loggedOut;
}

function reconnectDelayFor(code) {
    if (code === DisconnectReason.restartRequired) return 1500;
    if (code === DisconnectReason.timedOut || code === 408) return 2000;
    if (code === DisconnectReason.connectionClosed || code === 428) return 3000;
    if (code === DisconnectReason.connectionReplaced || code === 440) return 10000;
    if (code === DisconnectReason.badSession) return 5000;
    reconnectAttempts += 1;
    return Math.min(CONFIG.RECONNECT_MAX_MS, CONFIG.RECONNECT_BASE_MS + reconnectAttempts * 1500);
}

function scheduleReconnect(delayMs, reason) {
    if (retryTimer) clearTimeout(retryTimer);
    console.log(`[WhatsApp] Reconnect in ${Math.round(delayMs / 1000)}s (${reason})`);
    retryTimer = setTimeout(() => startWhatsApp().catch(() => {}), delayMs);
}

async function destroySocket(oldSock) {
    if (!oldSock) return;
    try { oldSock.ev.removeAllListeners(); } catch (_) { /* ignore */ }
    try { oldSock.ws?.close(); } catch (_) { /* ignore */ }
    try { oldSock.end?.(); } catch (_) { /* ignore */ }
}

function extractText(msg) {
    if (!msg?.message) return '';
    const m = msg.message;
    return (
        m.conversation
        || m.extendedTextMessage?.text
        || m.imageMessage?.caption
        || m.videoMessage?.caption
        || m.buttonsResponseMessage?.selectedDisplayText
        || m.listResponseMessage?.title
        || ''
    ).trim();
}

// Elige aleatoriamente entre varias variantes de respuesta para evitar mensajes repetitivos (anti-spam)
function pickRandom(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

function fallbackReply(text) {
    const t = text.toLowerCase();
    if (/\b(hola|buenas|buenas|buenos|buen|hey|ey)\b/.test(t)) {
        return pickRandom([
            'Hola! Bienvenido a TaxPiya 👋 ¿En qué te puedo ayudar?',
            'Hola, con gusto te atiendo. Cuéntame qué necesitas.',
            '¡Buenas! Soy el asistente de TaxPiya. ¿Cómo te puedo ayudar?',
            'Hola, aquí estoy. ¿En qué te puedo colaborar?',
        ]);
    }
    if (/\b(viaje|taxi|precio|tarifa|cuesta|valor)\b/.test(t)) {
        return pickRandom([
            'Para pedir un taxi, abre la app TaxPiya, elige tu origen y destino, y confirma la tarifa. ¡Es muy fácil!',
            'Desde la app puedes ver la tarifa exacta antes de confirmar el viaje. ¿Ya la tienes instalada?',
            'El precio lo calcula la app según tu ruta. Descárgala y ponlo a prueba!',
        ]);
    }
    if (/\b(ayuda|help|soporte|problema|error)\b/.test(t)) {
        return pickRandom([
            'Claro, cuéntame qué pasó y te ayudo a resolverlo.',
            'Con gusto te ayudo. ¿Qué problema tienes con la app?',
            'Dime qué está pasando y lo miramos juntos.',
        ]);
    }
    return pickRandom([
        'Gracias por escribirnos a TaxPiya! Puedes usar la app en taxpiya.onrender.com',
        '¡Hola! Si necesitas un taxi usa la app TaxPiya. ¿Te puedo ayudar con algo más?',
        'Recibido. Para servicios de taxi usa la app TaxPiya. ¿Algo más en qué colaborarte?',
    ]);
}

async function askGroq(userMessage) {
    const apiKey = env('GROQ_API_KEY');
    if (!apiKey) return fallbackReply(userMessage);
    try {
        const res = await fetch('https://api.groq.com/openai/v1/chat/completions', {
            method: 'POST',
            headers: { Authorization: `Bearer ${apiKey}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                model: env('GROQ_MODEL', 'llama-3.1-8b-instant'),
                messages: [
                    {
                        role: 'system',
                        content: `Eres el agente de soporte de TaxPiya, una app de taxis en Colombia. 
Respondes por WhatsApp como un humano real: usa un tono cálido, informal y cercano. 
Nunca uses frases robóticas como "Soy un asistente virtual" o "Como IA...". 
Escribe máximo 2 o 3 oraciones cortas. Usa emojis con moderación (1 o 2 máximo). 
Variá el inicio de tus respuestas para no sonar repetitivo. 
Temas que puedes resolver: viajes, tarifas, la app TaxPiya, cancelaciones, Nequi, código de verificación del viaje y soporte general.`,
                    },
                    { role: 'user', content: userMessage },
                ],
                temperature: 0.75,
                max_tokens: 220,
            }),
        });
        if (!res.ok) {
            console.error('[WhatsApp AI] Groq HTTP', res.status);
            return fallbackReply(userMessage);
        }
        const data = await res.json();
        return data.choices?.[0]?.message?.content?.trim() || fallbackReply(userMessage);
    } catch (e) {
        console.error('[WhatsApp AI] Groq error:', e.message);
        return fallbackReply(userMessage);
    }
}

function scheduleSessionBackup() {
    if (sessionBackupTimer) clearInterval(sessionBackupTimer);
    sessionBackupTimer = setInterval(() => {
        if (connectionStatus === 'connected') {
            backupSessionToGithub().catch(() => {});
        }
    }, CONFIG.BACKUP_MS);
}

function touchEvent(source) {
    lastEventAt = Date.now();
    if (source === 'message') lastMessageAt = lastEventAt;
}

function requestProcessRestart(reason) {
    console.warn(`[WhatsApp] Process restart requested: ${reason}`);
    releaseServiceLock();
    setTimeout(() => process.exit(0), 500);
}

function startWatchdog() {
    if (watchdogTimer) clearInterval(watchdogTimer);
    watchdogTimer = setInterval(() => {
        const now = Date.now();

        if (now - processStartedAt > CONFIG.PROCESS_RESTART_MS && connectionStatus === 'connected') {
            requestProcessRestart('scheduled-maintenance');
            return;
        }

        if (connectionStatus === 'connected') {
            lastConnectedAt = now;
            const idle = now - (lastHeartbeatAt || lastConnectedAt);
            if (idle > CONFIG.HEARTBEAT_MS - 60000 && sock) {
                sock.sendPresenceUpdate('available').then(() => {
                    lastHeartbeatAt = Date.now();
                    touchEvent('heartbeat');
                }).catch(() => {});
            }

            const silentFor = now - (lastEventAt || lastConnectedAt || processStartedAt);
            if (silentFor > CONFIG.DEAF_SESSION_MS) {
                console.warn(`[WhatsApp] Deaf session detected (${Math.round(silentFor / 60000)}m silence)`);
                requestProcessRestart('deaf-session');
            }
            return;
        }

        if (connectionStatus === 'connecting' || isStarting) return;
        const downFor = now - (lastDisconnectedAt || 0);
        if (downFor > 60000) {
            console.warn('[WhatsApp] Watchdog: forcing reconnect');
            reconnectAttempts = 0;
            startWhatsApp().catch(() => {});
        }
    }, CONFIG.WATCHDOG_MS);
}

function watchSessionFiles() {
    try {
        fs.watch(sessionDir, { persistent: false }, () => {
            if (watchDebounce) clearTimeout(watchDebounce);
            watchDebounce = setTimeout(() => {
                if (connectionStatus === 'connected') backupSessionToGithub().catch(() => {});
            }, 4000);
        });
    } catch (e) {
        console.warn('[WhatsApp Session] fs.watch unavailable:', e.message);
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
        await destroySocket(sock);
        sock = null;

        const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
        const { version } = await fetchLatestBaileysVersion();
        const logger = pino({ level: 'silent' });

        sock = makeWASocket({
            version,
            auth: {
                creds: state.creds,
                keys: makeCacheableSignalKeyStore(state.keys, logger),
            },
            logger,
            printQRInTerminal: false,
            connectTimeoutMs: 120000,
            defaultQueryTimeoutMs: 90000,
            keepAliveIntervalMs: CONFIG.KEEP_ALIVE_MS,
            retryRequestDelayMs: 500,
            // Identificarse como un Samsung Galaxy S23 con WhatsApp Web estándar
            browser: ['Samsung Galaxy S23', 'Chrome', '126.0.6478.126'],
            syncFullHistory: false,
            markOnlineOnConnect: false,
            generateHighQualityLinkPreview: false,
            // No mostrar presencia "en línea" al conectar
            shouldSyncHistoryMessage: () => false,
            shouldIgnoreJid: jid => jid.endsWith('@broadcast') || jid.endsWith('@g.us'),
            getMessage: async () => undefined,
        });

        sock.ev.on('creds.update', async () => {
            touchEvent('creds');
            await saveCreds();
            await backupSessionToGithub();
        });

        const userQueues = new Map();
        const lastReplied = new Map();

        async function processMessage(from, text, msgKey) {
            const now = Date.now();
            const lastTs = lastReplied.get(from) || 0;
            if (now - lastTs < CONFIG.RATE_LIMIT_MS) return;

            lastMessageAt = now;

            // 1. Simular tiempo de lectura (esperar de 2 a 4 segundos antes de "abrir" el chat y marcar azul)
            const initialDelay = Math.floor(Math.random() * 2000) + 2000;
            await sleep(initialDelay);
            if (!sock || connectionStatus !== 'connected') return;

            // Marcar mensaje como visto
            try { await sock.readMessages([msgKey]); } catch (_) { /* ignore */ }

            // Obtener respuesta de IA (toma unos 1-2 segundos, sirviendo como tiempo de "pensar")
            const reply = await askGroq(text);
            if (!sock || connectionStatus !== 'connected' || !reply) return;

            // 2. Simular pausa de "pensamiento" adicional después de leer y antes de escribir (1.2 a 2.5 segundos)
            const thinkingDelay = Math.floor(Math.random() * 1300) + 1200;
            await sleep(thinkingDelay);
            if (!sock || connectionStatus !== 'connected') return;

            // 3. Empezar a escribir ("escribiendo...")
            try { await sock.sendPresenceUpdate('composing', from); } catch (_) { /* ignore */ }

            // 4. Calcular tiempo de escritura real proporcional a la longitud de la respuesta (velocidad humana móvil: ~50-80ms por caracter)
            const msPerChar = Math.floor(Math.random() * 30) + 50;
            let typingDuration = reply.length * msPerChar;
            // Asegurar que tarde mínimo 3 segundos y máximo 12 segundos escribiendo
            typingDuration = Math.max(3000, Math.min(12000, typingDuration));

            await sleep(typingDuration);
            if (!sock || connectionStatus !== 'connected') return;

            // 5. Detener estado de escritura y enviar
            try { await sock.sendPresenceUpdate('paused', from); } catch (_) { /* ignore */ }
            await sock.sendMessage(from, { text: reply });

            lastReplied.set(from, Date.now());
            console.log(`[WhatsApp AI] Replied to ${from} (Typing duration: ${typingDuration}ms)`);
        }

        sock.ev.on('messages.upsert', async (m) => {
            touchEvent('message');
            if (m.type !== 'notify') return;
            for (const msg of m.messages) {
                if (msg.key.fromMe) continue;
                const from = msg.key.remoteJid;
                if (!from || from.endsWith('@g.us')) continue;
                const text = extractText(msg);
                if (!text) continue;

                const prev = userQueues.get(from) || Promise.resolve();
                const next = prev.then(() => processMessage(from, text, msg.key)).catch(err => {
                    console.error('[WhatsApp AI] process error:', err.message);
                });
                userQueues.set(from, next);
            }
        });

        sock.ev.on('connection.update', async (update) => {
            touchEvent('connection');
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                connectionStatus = 'qr';
                currentQr = qr;
                reconnectAttempts = 0;
                try { fs.writeFileSync(qrFilePath, qr); } catch (_) { /* ignore */ }
                console.log('[WhatsApp] QR ready — scan from admin panel');
            }

            if (connection === 'close') {
                currentQr = null;
                const oldSock = sock;
                sock = null;
                connectionStatus = 'disconnected';
                lastDisconnectedAt = Date.now();

                const code = disconnectCode(lastDisconnect);
                lastDisconnectCode = code;
                console.log(`[WhatsApp] Closed code=${code}`);

                if (shouldClearSession(code)) {
                    clearLocalSession();
                    reconnectAttempts = 0;
                    await clearGithubSessionBackup();
                    scheduleReconnect(4000, 'loggedOut');
                } else if (code === DisconnectReason.badSession) {
                    console.warn('[WhatsApp] badSession — restoring from GitHub');
                    clearLocalSession();
                    await restoreSessionFromGithub({ force: true });
                    scheduleReconnect(5000, 'badSession-restore');
                } else {
                    scheduleReconnect(reconnectDelayFor(code), `code=${code}`);
                }

                await destroySocket(oldSock);
            } else if (connection === 'open') {
                connectionStatus = 'connected';
                currentQr = null;
                reconnectAttempts = 0;
                lastConnectedAt = Date.now();
                lastDisconnectedAt = 0;
                lastHeartbeatAt = Date.now();
                touchEvent('open');
                try { fs.unlinkSync(qrFilePath); } catch (_) { /* ignore */ }
                myNumber = sock?.user?.id?.split(':')[0] ?? 'unknown';
                console.log(`[WhatsApp] Connected as ${myNumber}`);
                await backupSessionToGithub();
            }
        });
    } catch (err) {
        console.error('[WhatsApp] Startup error:', err.message);
        connectionStatus = 'disconnected';
        sock = null;
        scheduleReconnect(reconnectDelayFor(null), 'startup-error');
    } finally {
        isStarting = false;
    }
}

async function bootstrap() {
    if (!acquireServiceLock()) {
        console.error('[WhatsApp] Another instance running — exit');
        process.exit(0);
    }

    console.log('[WhatsApp] v3 stable boot (deaf-session watchdog + process restart)');
    console.log('[WhatsApp] GitHub token:', env('GITHUB_BACKUP_TOKEN') ? 'yes' : 'NO');

    await restoreSessionFromGithub();
    scheduleSessionBackup();
    watchSessionFiles();
    startWatchdog();
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
        lastDisconnectCode,
        lastConnectedAt,
        lastMessageAt,
        lastEventAt,
        processStartedAt,
        sessionFiles: pathExists(sessionDir) ? fs.readdirSync(sessionDir).length : 0,
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
            return res.status(400).json({ ok: false, message: 'Phone and message required' });
        }
        if (connectionStatus !== 'connected' || !sock) {
            return res.status(503).json({ ok: false, message: 'WhatsApp not connected: ' + connectionStatus });
        }
        const jid = String(phone).replace(/\D/g, '') + '@s.whatsapp.net';
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
        await destroySocket(sock);
        sock = null;
        clearLocalSession();
        await clearGithubSessionBackup();
        reconnectAttempts = 0;
        scheduleReconnect(3000, 'logout');
        res.json({ ok: true, message: 'Logged out' });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

app.post('/reconnect', async (req, res) => {
    try {
        if (connectionStatus === 'connected') {
            return res.json({ ok: true, message: 'Already connected', status: connectionStatus });
        }
        reconnectAttempts = 0;
        await startWhatsApp();
        res.json({ ok: true, message: 'Reconnect triggered', status: connectionStatus });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

app.post('/restart', (req, res) => {
    res.json({ ok: true, message: 'Process restart scheduled' });
    requestProcessRestart('api-restart');
});

const port = process.env.WHATSAPP_PORT || 8051;
app.listen(port, '127.0.0.1', () => {
    console.log(`[WhatsApp] Listening on ${port}`);
});
