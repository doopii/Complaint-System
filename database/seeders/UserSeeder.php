<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@campus.edu',
            'student_id' => null,
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        // Create student user
        User::create([
            'name' => 'John Student',
            'email' => 'john@student.edu',
            'student_id' => 'STU123456',
            'role' => 'student',
            'password' => Hash::make('password123'),
        ]);

        // Create another student user
        User::create([
            'name' => 'Jane Student',
            'email' => 'jane@student.edu',
            'student_id' => 'STU789012',
            'role' => 'student',
            'password' => Hash::make('password123'),
        ]);
    }
}
