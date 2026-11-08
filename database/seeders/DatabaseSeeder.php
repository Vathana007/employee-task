<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Full system access',
                'permissions' => Role::defaultPermissionsFor('admin'),
            ],
            [
                'name' => 'head',
                'description' => 'Department lead with project oversight',
                'permissions' => Role::defaultPermissionsFor('head'),
            ],
            [
                'name' => 'senior',
                'description' => 'Senior team member with project responsibilities',
                'permissions' => Role::defaultPermissionsFor('senior'),
            ],
            [
                'name' => 'user',
                'description' => 'Standard employee with assigned work',
                'permissions' => Role::defaultPermissionsFor('user'),
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description'], 'permissions' => $role['permissions']],
            );
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);
    }
}
