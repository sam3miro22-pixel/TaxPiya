#!/usr/bin/env node
/**
 * Restaura sesión WhatsApp desde GitHub antes de arrancar el servicio (Render redeploy).
 * Uso: node scripts/whatsapp-restore-session.js [--force]
 */
const { ensureDirs, restoreSessionFromGithub, hasLocalSession } = require('./whatsapp-session-store');

async function main() {
    const force = process.argv.includes('--force');
    ensureDirs();

    if (!force && hasLocalSession()) {
        console.log('[WhatsApp Restore] Sesión local ya existe — OK.');
        process.exit(0);
    }

    const ok = await restoreSessionFromGithub({ force });
    process.exit(ok ? 0 : 0);
}

main().catch(err => {
    console.error('[WhatsApp Restore] Error:', err.message);
    process.exit(0);
});
