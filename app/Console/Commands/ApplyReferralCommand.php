<?php

namespace App\Console\Commands;

use App\Services\ReferralService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApplyReferralCommand extends Command
{
    protected $signature = 'taxpiya:referral-apply {email : Email del referido} {code : Código TXP-P...}';

    protected $description = 'Vincula manualmente un referido y acredita bono pendiente';

    public function handle(ReferralService $referrals): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $code  = $referrals->normalizeCode((string) $this->argument('code'));

        if (!$code) {
            $this->error('Código inválido.');
            return self::FAILURE;
        }

        $check = $referrals->validateCode($code);
        if (!$check['ok']) {
            $this->error($check['message'] ?? 'Código no válido');
            return self::FAILURE;
        }

        $user = DB::table('users')->whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$user) {
            $this->error("No hay usuario con email {$email}");
            return self::FAILURE;
        }

        $result = $referrals->applyPasajeroReferral($code, (int) $user->id, false, true);
        if (!$result['ok']) {
            $this->error($result['message'] ?? 'No se pudo aplicar');
            return self::FAILURE;
        }

        $this->info('Referido aplicado para user #' . $user->id);
        if (!empty($result['referido_id'])) {
            $this->line('  referido_id: ' . $result['referido_id']);
        }
        if (!empty($result['skipped'])) {
            $this->warn('  (ya existía vínculo de referido)');
        }

        return self::SUCCESS;
    }
}
