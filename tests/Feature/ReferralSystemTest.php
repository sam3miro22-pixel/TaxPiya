<?php

namespace Tests\Feature;

use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReferralSystemTest extends TestCase
{
    public function test_normalize_and_validate_referral_code(): void
    {
        $service = app(ReferralService::class);
        $this->assertNull($service->normalizeCode('   '));
        $this->assertSame('TXP-P000001', $service->normalizeCode(' txp-p000001 '));

        $invalid = $service->validateCode('TXP-P999999');
        $this->assertFalse($invalid['ok']);
    }

    public function test_register_referral_links_users(): void
    {
        if (!Schema::hasTable('referidos')) {
            $this->markTestSkipped('Tabla referidos no disponible');
        }

        $service = app(ReferralService::class);

        $referrerId = DB::table('users')->insertGetId([
            'name' => 'Referrer Test',
            'email' => 'referrer_' . uniqid() . '@test.local',
            'telefono' => '399' . random_int(1000000, 9999999),
            'password' => bcrypt('test'),
            'estado' => 1,
            'user_role_id' => 2,
        ]);
        $referredId = DB::table('users')->insertGetId([
            'name' => 'Referred Test',
            'email' => 'referred_' . uniqid() . '@test.local',
            'telefono' => '398' . random_int(1000000, 9999999),
            'password' => bcrypt('test'),
            'estado' => 1,
            'user_role_id' => 2,
        ]);

        $code = $service->ensureUserCode($referrerId);
        $result = $service->registerReferral($code, $referredId, 'pasajero');

        $this->assertTrue($result['ok']);
        $this->assertDatabaseHas('referidos', [
            'referred_user_id' => $referredId,
            'codigo_usado' => $code,
            'tipo_referido' => 'pasajero',
            'estado' => 'activo',
        ]);

        if (Schema::hasTable('wallet_cuentas')) {
            $bonus = $service->payReferralBonus((int) $result['referido_id']);
            $this->assertTrue($bonus['ok'] || !empty($bonus['already_paid']));
        }
    }
}
