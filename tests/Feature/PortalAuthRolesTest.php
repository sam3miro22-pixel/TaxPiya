<?php

namespace Tests\Feature;

use App\Services\PortalAuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PortalAuthRolesTest extends TestCase
{
    public function test_pasajero_cannot_login_as_conductor(): void
    {
        $userId = DB::table('users')->insertGetId([
            'name'         => 'Solo Pasajero',
            'email'        => 'solo_pax_' . uniqid() . '@test.local',
            'telefono'     => '395' . random_int(1000000, 9999999),
            'password'     => bcrypt('test'),
            'estado'       => 1,
            'user_role_id' => 2,
        ]);

        $user = \App\Models\Users::find($userId);
        $portal = app(PortalAuthService::class);
        $this->assertFalse($portal->userMatchesPortal($user, 'conductor'));
        $this->assertSame('Acceso exclusivo para Conductores.', $portal->validateRoleForPortal($user, 'conductor'));
    }

    public function test_conductor_cannot_login_as_pasajero(): void
    {
        if (!Schema::hasTable('conductores')) {
            $this->markTestSkipped('Tabla conductores no disponible');
        }

        $userId = DB::table('users')->insertGetId([
            'name'         => 'Solo Conductor',
            'email'        => 'solo_drv_' . uniqid() . '@test.local',
            'telefono'     => '394' . random_int(1000000, 9999999),
            'password'     => bcrypt('test'),
            'estado'       => 1,
            'user_role_id' => 3,
        ]);

        DB::table('conductores')->insert([
            'user_id'          => $userId,
            'estado_operitivo' => 1,
            'disponible'       => 0,
            'total_viajes'     => 0,
            'created_at'       => now()->toDateTimeString(),
            'updated_at'       => now()->toDateTimeString(),
        ]);

        $user = \App\Models\Users::find($userId);
        $portal = app(PortalAuthService::class);
        $this->assertFalse($portal->userMatchesPortal($user, 'pasajero'));
        $this->assertSame('Este acceso es solo para Pasajeros.', $portal->validateRoleForPortal($user, 'pasajero'));
    }

    public function test_empresa_requires_empresa_role(): void
    {
        $userId = DB::table('users')->insertGetId([
            'name'         => 'No Empresa',
            'email'        => 'no_emp_' . uniqid() . '@test.local',
            'telefono'     => '393' . random_int(1000000, 9999999),
            'password'     => bcrypt('test'),
            'estado'       => 1,
            'user_role_id' => 2,
        ]);

        $user = \App\Models\Users::find($userId);
        $portal = app(PortalAuthService::class);
        $this->assertFalse($portal->userMatchesPortal($user, 'empresa'));
        $this->assertSame('Acceso exclusivo para empresas afiliadas.', $portal->validateRoleForPortal($user, 'empresa'));
    }
}
