<?php

namespace App\Console\Commands;

use App\Services\ReferralService;
use Illuminate\Console\Command;

class PayPendingReferralBonusesCommand extends Command
{
    protected $signature = 'taxpiya:referral-pay-pending {--user= : ID del referidor (opcional)}';

    protected $description = 'Acredita bonos de referidos pendientes a la billetera';

    public function handle(ReferralService $referrals): int
    {
        $userId = $this->option('user');
        if ($userId !== null && $userId !== '') {
            $paid = $referrals->processPendingBonusesForReferrerUser((int) $userId);
            $this->info("Bonos acreditados para user #{$userId}: {$paid}");

            return self::SUCCESS;
        }

        $paid = $referrals->backfillAllUnpaidBonuses();
        $this->info("Bonos referidos acreditados: {$paid}");

        return self::SUCCESS;
    }
}
