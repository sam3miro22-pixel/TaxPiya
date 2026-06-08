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

    public function test_apply_referral_on_existing_user_when_reregistering(): void
    {
        if (!Schema::hasTable('referidos')) {
            $this->markTestSkipped('Tabla referidos no disponible');
        }

        $service = app(ReferralService::class);

        $referrerId = DB::table('users')->insertGetId([
            'name' => 'Referrer Rereg',
            'email' => 'referrer_rereg_' . uniqid() . '@test.local',
            'telefono' => '397' . random_int(1000000, 9999999),
            'password' => bcrypt('test'),
            'estado' => 1,
            'user_role_id' => 2,
        ]);

        $referredId = DB::table('users')->insertGetId([
            'name' => 'Referred Rereg',
            'email' => 'referred_rereg_' . uniqid() . '@test.local',
            'telefono' => '396' . random_int(1000000, 9999999),
            'password' => bcrypt('test'),
            'estado' => 1,
            'user_role_id' => 2,
            'firebase_uid' => 'old_firebase_uid_' . uniqid(),
        ]);

        $code = $service->ensureUserCode($referrerId);
        $this->assertFalse($service->userHasReferral($referredId));

        $result = $service->applyPasajeroReferral($code, $referredId, false, true);

        $this->assertTrue($result['ok']);
        $this->assertTrue($service->userHasReferral($referredId));
        $this->assertDatabaseHas('referidos', [
            'referred_user_id' => $referredId,
            'referrer_user_id' => $referrerId,
            'estado' => 'activo',
        ]);
    }
}
