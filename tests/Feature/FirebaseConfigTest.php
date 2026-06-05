<?php

namespace Tests\Feature;

use Tests\TestCase;

class FirebaseConfigTest extends TestCase
{
    public function test_firebase_config_structure_exists(): void
    {
        $this->assertIsArray(config('firebase'));
        $this->assertArrayHasKey('project_id', config('firebase'));
        $this->assertArrayHasKey('web', config('firebase'));
    }

    public function test_firebase_sync_route_is_registered(): void
    {
        $this->assertNotNull(route('auth.firebase.sync', [], false));
    }

    public function test_firebase_sync_rejects_missing_token(): void
    {
        $response = $this->postJson('/auth/firebase/sync', []);

        $response->assertStatus(422);
    }

    public function test_home_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
