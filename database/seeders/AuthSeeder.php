<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Admin;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create test student user
        $studentUser = User::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'student',
        ]);

        Student::create([
            'student_id' => 'STU001',
            'name' => 'John Doe',
            'email' => 'student@example.com',
            'course' => 'Computer Science',
            'year_level' => 3,
            'department' => 'College of Engineering',
        ]);

        // Create test admin user
        $adminUser = User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
        ]);

        Admin::create([
            'name' => 'Jane Smith',
            'email' => 'admin@example.com',
            'department' => 'Student Affairs',
            'position' => 'Complaint Manager',
        ]);
    }
}
