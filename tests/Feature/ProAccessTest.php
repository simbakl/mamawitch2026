<?php

namespace Tests\Feature;

use App\Models\ProAccount;
use App\Models\ProType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\ProContentSeeder::class);
    }

    public function test_pro_request_page_returns_200(): void
    {
        $this->get('/pro/demande')->assertOk();
    }

    public function test_pro_request_form_submits_successfully(): void
    {
        $proType = ProType::first();

        $response = $this->post('/pro/demande', [
            'first_name' => 'Pierre',
            'last_name' => 'Martin',
            'email' => 'pierre@venue.com',
            'structure' => 'La Salle de Concert',
            'pro_type_id' => $proType->id,
            'message' => 'Intéressé pour un booking',
            'honeypot' => '',
        ]);

        $response->assertRedirect('/pro/demande');
        $this->assertDatabaseHas('pro_accounts', [
            'email' => 'pierre@venue.com',
            'status' => 'pending',
        ]);
    }

    public function test_pro_request_rejects_duplicate_email(): void
    {
        $proType = ProType::first();

        ProAccount::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@venue.com',
            'structure' => 'Test',
            'pro_type_id' => $proType->id,
            'status' => 'pending',
        ]);

        $response = $this->post('/pro/demande', [
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'existing@venue.com',
            'structure' => 'Other',
            'pro_type_id' => $proType->id,
            'honeypot' => '',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_pro_dashboard_requires_auth(): void
    {
        $this->get('/pro')->assertRedirect('/login');
    }

    public function test_pro_dashboard_requires_pro_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)->get('/pro')->assertRedirect('/pro/demande');
    }

    public function test_pro_dashboard_accessible_with_approved_account(): void
    {
        $user = User::factory()->create();
        $user->assignRole('pro');

        $proType = ProType::first();
        ProAccount::create([
            'user_id' => $user->id,
            'first_name' => 'Pro',
            'last_name' => 'User',
            'email' => $user->email,
            'structure' => 'Media',
            'pro_type_id' => $proType->id,
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $this->actingAs($user)->get('/pro')->assertOk();
    }

    public function test_pro_invalid_invitation_token_rejected(): void
    {
        $this->get('/pro/invitation/invalid-token')
            ->assertRedirect('/pro/demande');
    }
}
