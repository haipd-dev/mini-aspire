<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    public function test_missing_user_name()
    {
        $response = $this->postJson('api/auth/get-token', [
            'password' => 'testpassword'
        ]);
        $response->assertStatus(422);
    }

    public function test_missing_password()
    {
        $response = $this->postJson('api/auth/get-token', [
            'username' => 'testusername'
        ]);
        $response->assertStatus(422);
    }

    public function test_wrong_username_or_password()
    {
        $response = $this->postJson('api/auth/get-token', [
            'username' => 'user1',
            'password' => 'nopassword'
        ]);
        $response->assertStatus(401);

        $response = $this->postJson('api/auth/get-token', [
            'username' => 'user1',
            'password' => 'password2'
        ]);
        $response->assertStatus(401);

        $response = $this->postJson('api/auth/get-token', [
            'username' => 'user3',
            'password' => 'password2'
        ]);
        $response->assertStatus(401);
    }

    public function test_valid_user_name_and_password()
    {
        $response = $this->postJson('api/auth/get-token', [
            'username' => 'user1',
            'password' => 'password1'
        ]);
        $response->assertStatus(200);
        $expireTime = config('sanctum.sanctum');
        $response->assertJson(function (AssertableJson $json){
            $json->hasAll(['access_token', 'token_type', 'expires_in']);
        }
        );
        $response->assertJsonPath('token_type', 'Bearer');
        $response->assertJsonPath('expires_in', $expireTime);
    }
}
