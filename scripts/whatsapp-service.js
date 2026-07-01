const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
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
if (!fs.existsSync(path.join(__dirname, '../storage'))) {
    fs.mkdirSync(path.join(__dirname, '../storage'));
}
if (!fs.existsSync(path.join(__dirname, '../storage/app'))) {
    fs.mkdirSync(path.join(__dirname, '../storage/app'));
}

let sock = null;
let connectionStatus = 'disconnected'; // 'disconnected' | 'connecting' | 'qr' | 'connected'
let currentQr = null;
let myNumber = null;

async function startWhatsApp() {
    connectionStatus = 'connecting';
    const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
    
    const logger = pino({ level: 'silent' });
    
    sock = makeWASocket({
        auth: state,
        logger: logger,
        printQRInTerminal: false,
    });
    
    sock.ev.on('creds.update', saveCreds);
    
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            connectionStatus = 'qr';
            currentQr = qr;
            fs.writeFileSync(qrFilePath, qr);
        }
        
        if (connection === 'close') {
            currentQr = null;
            const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log('Connection closed. Reconnecting...', shouldReconnect);
            connectionStatus = 'disconnected';
            
            if (shouldReconnect) {
                setTimeout(startWhatsApp, 4000);
            } else {
                // Logged out: clean up session files
                try {
                    fs.rmSync(sessionDir, { recursive: true, force: true });
                } catch(e){}
                try {
                    fs.unlinkSync(qrFilePath);
                } catch(e){}
                setTimeout(startWhatsApp, 4000);
            }
        } else if (connection === 'open') {
            connectionStatus = 'connected';
            currentQr = null;
            try {
                fs.unlinkSync(qrFilePath);
            } catch(e){}
            const user = sock.user;
            myNumber = user.id.split(':')[0];
            console.log('WhatsApp connection open! Logged in as:', myNumber);
        }
    });
}

// Start Baileys immediately
startWhatsApp();

// API Endpoints
app.get('/status', async (req, res) => {
    let qrImage = null;
    if (currentQr) {
        try {
            qrImage = await qrcode.toDataURL(currentQr);
        } catch (e) {
            console.error('Failed to generate QR Image:', e);
        }
    }
    
    res.json({
        ok: true,
        status: connectionStatus,
        qr: currentQr,
        qrImage: qrImage,
        user: myNumber
    });
});

app.post('/send', async (req, res) => {
    try {
        const { phone, message } = req.body;
        if (!phone || !message) {
            return res.status(400).json({ ok: false, message: 'Phone and message are required' });
        }
        
        if (connectionStatus !== 'connected' || !sock) {
            return res.status(503).json({ ok: false, message: 'WhatsApp service not connected' });
        }
        
        // Format phone: remove any leading +, spacing, etc., and append @s.whatsapp.net
        let formattedPhone = phone.replace(/\D/g, '');
        if (!formattedPhone.endsWith('@s.whatsapp.net')) {
            formattedPhone = `${formattedPhone}@s.whatsapp.net`;
        }
        
        await sock.sendMessage(formattedPhone, { text: message });
        res.json({ ok: true, message: 'Message sent successfully' });
    } catch (e) {
        console.error('Error sending message:', e);
        res.status(500).json({ ok: false, error: e.message });
    }
});

app.post('/logout', async (req, res) => {
    try {
        currentQr = null;
        myNumber = null;
        connectionStatus = 'disconnected';
        
        if (sock) {
            try {
                await sock.logout();
            } catch(e){}
            sock = null;
        }
        
        // Clean session directory
        try {
            fs.rmSync(sessionDir, { recursive: true, force: true });
        } catch(e){}
        try {
            fs.unlinkSync(qrFilePath);
        } catch(e){}
        
        // Restart WhatsApp to generate a new QR code
        setTimeout(startWhatsApp, 2000);
        
        res.json({ ok: true, message: 'Logged out and session cleared' });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

const port = process.env.WHATSAPP_PORT || 8051;
app.listen(port, () => {
    console.log(`WhatsApp Baileys service listening on port ${port}`);
});
