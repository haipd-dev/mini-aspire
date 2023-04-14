<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {username} {password} {--is-admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isAdmin = $this->option('is-admin');
        $username = $this->argument('username');
        $password = $this->argument('password');
        $password = Hash::make($password);
        User::factory()->create(['username' => $username, 'password' => $password, 'user_type' => $isAdmin ? User::TYPE_ADMIN : User::TYPE_CUSTOMER]);
        $this->info("User created");
    }
}
