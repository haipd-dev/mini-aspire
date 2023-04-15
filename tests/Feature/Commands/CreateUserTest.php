<?php

namespace Tests\Feature\Commands;

use App\Models\User;
use Tests\Feature\AbstractFeatureTest;

class CreateUserTest extends AbstractFeatureTest
{
    public function test_error_when_duplicate_user_name()
    {
        $user = $this->createCustomerUser();
        $this->artisan("user:create {$user->username} password")
            ->assertExitCode(0)
            ->assertSuccessful()
            ->expectsOutput("The username {$user->username} has been already existed");
//        $username = $user->username
    }

    public function test_create_admin_user()
    {
        $this->artisan('user:create username password --is-admin')
            ->assertExitCode(0)
            ->assertSuccessful()
            ->expectsOutput('User created');
        $this->assertDatabaseHas('users', ['username' => 'username', 'user_type' => User::TYPE_ADMIN]);
    }

    public function test_create_customer_user()
    {
        $this->artisan('user:create username1 password')
            ->assertExitCode(0)
            ->assertSuccessful()
            ->expectsOutput('User created');
        $this->assertDatabaseHas('users', ['username' => 'username1', 'user_type' => User::TYPE_CUSTOMER]);
    }
}
