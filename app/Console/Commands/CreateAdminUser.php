<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {username} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check if the user already exists using the username
        if (User::where('username', $this->argument('username'))->exists()) {
            $this->error('Error: A user with this username already exists.');
            return 1;
        }

        // Create the new user with the correct table fields
        $user = User::create([
            'username' => $this->argument('username'),
            'password' => Hash::make($this->argument('password')),
            'fullname' => 'Admin User', // Default full name
            'email' => $this->argument('username') . '@yourdomain.com', // Assuming email can be generated from username
            'phone' => null, // Phone can be null
            'role' => 'admin', // Assign the 'admin' role
            'status' => 'active', // Set status to 'active'
        ]);

        $this->info('Admin user created successfully!');
        $this->info("Username: {$user->username}");
        $this->info("Role: {$user->role}");
        return 0;
    }
}

