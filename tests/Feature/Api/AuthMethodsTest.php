<?php

namespace Tests\Feature\Api;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthMethodsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_supports_phone_with_password(): void
    {
        $user = User::factory()->create([
            'phone' => '+919999999999',
        ]);

        $response = $this->postJson('/api/login', [
            'login' => $user->phone,
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'user', 'tenant', 'allowed_login_methods']);
    }

    public function test_api_login_is_blocked_when_email_password_auth_is_disabled(): void
    {
        Setting::create([
            'key' => 'email_password_auth_enabled',
            'value' => 'false',
            'group' => 'auth',
        ]);

        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'Email/password login is currently disabled by admin',
                'allowed_login_methods' => [
                    'email_password' => false,
                    'phone_otp' => false,
                ],
            ]);
    }

    public function test_firebase_login_is_blocked_when_phone_otp_auth_is_disabled(): void
    {
        Setting::create([
            'key' => 'firebase_enabled',
            'value' => 'false',
            'group' => 'firebase',
        ]);
        Setting::create([
            'key' => 'firebase_phone_auth_enabled',
            'value' => 'false',
            'group' => 'auth',
        ]);

        $response = $this->postJson('/api/auth/firebase', [
            'firebase_id_token' => 'dummy-token',
        ]);

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'Phone OTP login is currently disabled by admin',
                'allowed_login_methods' => [
                    'email_password' => true,
                    'phone_otp' => false,
                ],
            ]);
    }

    public function test_authenticated_user_can_update_push_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/push/token', [
            'fcm_token' => 'demo-token-123',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Push token updated successfully',
                'fcm_token' => 'demo-token-123',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'fcm_token' => 'demo-token-123',
        ]);
    }
}
