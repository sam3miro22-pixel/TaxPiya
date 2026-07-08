/**
 * Persistencia de sesión WhatsApp (Baileys) en GitHub.
 * Compartido entre whatsapp-service.js y whatsapp-restore-session.js
 */
const fs = require('fs');
const path = require('path');
const https = require('https');

const sessionDir = path.join(__dirname, '../storage/app/whatsapp-session');
const qrFilePath = path.join(__dirname, '../storage/app/whatsapp-qr.txt');

function ensureDirs() {
    [path.join(__dirname, '../storage'), path.join(__dirname, '../storage/app'), sessionDir].forEach(dir => {
        if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    });
}

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
    } catch (_) { /* ignore */ }
    Object.assign(_envCache, process.env);
    return _envCache;
}

function env(key, fallback = '') {
    return (loadEnv()[key] ?? process.env[key] ?? fallback).toString().trim();
}

function githubHeaders() {
    return {
        Authorization: `Bearer ${env('GITHUB_BACKUP_TOKEN')}`,
        Accept: 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
        'User-Agent': 'TaxPiya-WhatsApp-Session',
        'Content-Type': 'application/json',
    };
}

function sessionContentsUrl() {
    const owner = env('GITHUB_BACKUP_OWNER', 'sam3miro22-pixel');
    const repo = env('GITHUB_BACKUP_REPO', 'taxpiya-db-backup');
    return `https://api.github.com/repos/${owner}/${repo}/contents/whatsapp-session.tar.gz.b64`;
}

function githubRequest(method, url, body) {
    return new Promise((resolve, reject) => {
        const u = new URL(url);
        const opts = {
            hostname: u.hostname,
            path: u.pathname + u.search,
            method,
            headers: githubHeaders(),
        };
        const req = https.request(opts, res => {
            let data = '';
            res.on('data', c => { data += c; });
            res.on('end', () => resolve({ status: res.statusCode, body: data }));
        });
        req.on('error', reject);
        if (body) req.write(typeof body === 'string' ? body : JSON.stringify(body));
        req.end();
    });
}

function listSessionFiles() {
    if (!fs.existsSync(sessionDir)) return [];
    return fs.readdirSync(sessionDir).filter(f => {
        const fp = path.join(sessionDir, f);
        return fs.statSync(fp).isFile();
    });
}

function hasLocalSession() {
    const files = listSessionFiles();
    return files.some(f => f.startsWith('creds.json') || f.includes('pre-key') || f.includes('session'));
}

function packSessionMap() {
    const sessionMap = {};
    for (const f of listSessionFiles()) {
        sessionMap[f] = fs.readFileSync(path.join(sessionDir, f)).toString('base64');
    }
    return sessionMap;
}

async function backupSessionToGithub() {
    const token = env('GITHUB_BACKUP_TOKEN');
    if (!token) {
        console.warn('[WhatsApp Session] GITHUB_BACKUP_TOKEN no configurado — no se puede respaldar.');
        return false;
    }

    try {
        ensureDirs();
        const files = listSessionFiles();
        if (files.length === 0) return false;

        const packed = Buffer.from(JSON.stringify(packSessionMap())).toString('base64');

        let sha = null;
        const getMeta = await githubRequest('GET', sessionContentsUrl(), null);
        if (getMeta.status === 200) {
            try { sha = JSON.parse(getMeta.body).sha; } catch (_) { /* ignore */ }
        }

        const payload = {
            message: `WhatsApp session backup ${new Date().toISOString()}`,
            content: packed,
        };
        if (sha) payload.sha = sha;

        const put = await githubRequest('PUT', sessionContentsUrl(), payload);
        if (put.status < 300) {
            console.log(`[WhatsApp Session] Backup OK (${files.length} archivos).`);
            return true;
        }
        console.error('[WhatsApp Session] GitHub backup failed:', put.status, put.body.slice(0, 200));
        return false;
    } catch (e) {
        console.error('[WhatsApp Session] Backup error:', e.message);
        return false;
    }
}

async function restoreSessionFromGithub({ force = false } = {}) {
    const token = env('GITHUB_BACKUP_TOKEN');
    if (!token) {
        console.log('[WhatsApp Session] Sin GITHUB_BACKUP_TOKEN — omitiendo restore.');
        return false;
    }

    if (!force && hasLocalSession()) {
        console.log('[WhatsApp Session] Sesión local presente — no se sobrescribe desde GitHub.');
        return true;
    }

    try {
        ensureDirs();
        const getMeta = await githubRequest('GET', sessionContentsUrl(), null);
        if (getMeta.status === 404) {
            console.log('[WhatsApp Session] No hay backup en GitHub (primera vez).');
            return false;
        }
        if (getMeta.status >= 300) {
            console.error('[WhatsApp Session] GitHub restore GET failed:', getMeta.status);
            return false;
        }

        const meta = JSON.parse(getMeta.body);
        const packed = Buffer.from(meta.content.replace(/\s/g, ''), 'base64').toString('utf8');
        const sessionMap = JSON.parse(packed);

        fs.rmSync(sessionDir, { recursive: true, force: true });
        fs.mkdirSync(sessionDir, { recursive: true });

        for (const [filename, b64] of Object.entries(sessionMap)) {
            fs.writeFileSync(path.join(sessionDir, filename), Buffer.from(b64, 'base64'));
        }

        console.log(`[WhatsApp Session] Restaurados ${Object.keys(sessionMap).length} archivos desde GitHub.`);
        return true;
    } catch (e) {
        console.error('[WhatsApp Session] Restore error:', e.message);
        return false;
    }
}

async function clearGithubSessionBackup() {
    const token = env('GITHUB_BACKUP_TOKEN');
    if (!token) return;

    try {
        const getMeta = await githubRequest('GET', sessionContentsUrl(), null);
        if (getMeta.status !== 200) return;
        const sha = JSON.parse(getMeta.body).sha;
        await githubRequest('DELETE', sessionContentsUrl(), {
            message: 'WhatsApp session cleared (logged out)',
            sha,
        });
    } catch (e) {
        console.error('[WhatsApp Session] Clear backup error:', e.message);
    }
}

function clearLocalSession() {
    try { fs.rmSync(sessionDir, { recursive: true, force: true }); } catch (_) { /* ignore */ }
    try { fs.unlinkSync(qrFilePath); } catch (_) { /* ignore */ }
    ensureDirs();
}

module.exports = {
    sessionDir,
    qrFilePath,
    ensureDirs,
    env,
    hasLocalSession,
    backupSessionToGithub,
    restoreSessionFromGithub,
    clearGithubSessionBackup,
    clearLocalSession,
};
