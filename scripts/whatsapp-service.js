const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const express = require('express');
const qrcode = require('qrcode');
const fs = require('fs');
const path = require('path');
const pino = require('pino');

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
let reconnectDelay = 4000;  // Start at 4s, back off on repeated failures
let retryTimer = null;

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

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                connectionStatus = 'qr';
                currentQr = qr;
                reconnectDelay = 4000;  // Reset delay on QR received (service is healthy)
                try { fs.writeFileSync(qrFilePath, qr); } catch(e) {}
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
                    // Wipe session so a new QR is generated
                    try { fs.rmSync(sessionDir, { recursive: true, force: true }); } catch(e) {}
                    try { fs.unlinkSync(qrFilePath); } catch(e) {}
                    reconnectDelay = 4000;
                }

                // Exponential backoff up to 60s
                reconnectDelay = Math.min(reconnectDelay * 1.5, 60000);
                console.log(`[WhatsApp] Reconnecting in ${Math.round(reconnectDelay / 1000)}s...`);
                retryTimer = setTimeout(startWhatsApp, reconnectDelay);

            } else if (connection === 'open') {
                connectionStatus = 'connected';
                currentQr = null;
                reconnectDelay = 4000;  // Reset delay
                try { fs.unlinkSync(qrFilePath); } catch(e) {}
                const user = sock.user;
                myNumber = user?.id?.split(':')[0] ?? 'unknown';
                console.log(`[WhatsApp] Connected as: ${myNumber}`);
            }
        });

    } catch (err) {
        console.error('[WhatsApp] Fatal startup error:', err.message);
        connectionStatus = 'disconnected';
        reconnectDelay = Math.min(reconnectDelay * 1.5, 60000);
        retryTimer = setTimeout(startWhatsApp, reconnectDelay);
    }
}

// Kick off connection
startWhatsApp();

// API Endpoints
app.get('/status', async (req, res) => {
    let qrImage = null;
    if (currentQr) {
        try { qrImage = await qrcode.toDataURL(currentQr); } catch (e) {}
    }
    res.json({ ok: true, status: connectionStatus, qr: currentQr, qrImage, user: myNumber });
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
            try { await sock.logout(); } catch(e) {}
            sock = null;
        }
        try { fs.rmSync(sessionDir, { recursive: true, force: true }); } catch(e) {}
        try { fs.unlinkSync(qrFilePath); } catch(e) {}
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
