<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create default permissions
        $permissions = [
            // Dashboard permissions
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'group' => 'dashboard'],
            
            // Plot permissions
            ['name' => 'View Plots', 'slug' => 'view-plots', 'group' => 'plots'],
            ['name' => 'Manage Plots', 'slug' => 'manage-plots', 'group' => 'plots'],
            
            // Company permissions
            ['name' => 'View Companies', 'slug' => 'view-companies', 'group' => 'companies'],
            ['name' => 'Manage Companies', 'slug' => 'manage-companies', 'group' => 'companies'],
            
            // Admin permissions
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'group' => 'admin'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'group' => 'admin'],
            ['name' => 'View Logs', 'slug' => 'view-logs', 'group' => 'admin'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create admin role
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full access to all features',
            'is_admin' => true,
            'is_game_role' => false,
        ]);

        // Give admin all permissions
        $adminRole->permissions()->attach(Permission::all());

        // Create default player role
        $playerRole = Role::create([
            'name' => 'Player',
            'slug' => 'player',
            'description' => 'Default role for all players',
            'is_admin' => false,
            'is_game_role' => false,
        ]);

        // Give players basic permissions
        $playerRole->permissions()->attach(
            Permission::whereIn('slug', [
                'view-dashboard',
                'view-plots',
                'view-companies'
            ])->get()
        );

        // Create some game roles
        $gameRoles = [
            [
                'name' => 'Police Officer',
                'description' => 'Law enforcement role',
                'permissions' => ['view-plots']
            ],
            [
                'name' => 'Chamber of Commerce',
                'description' => 'Can manage company registrations',
                'permissions' => ['view-companies', 'manage-companies']
            ]
        ];

        foreach ($gameRoles as $role) {
            $gameRole = Role::create([
                'name' => $role['name'],
                'slug' => Str::slug($role['name']),
                'description' => $role['description'],
                'is_admin' => false,
                'is_game_role' => true,
            ]);

            $gameRole->permissions()->attach(
                Permission::whereIn('slug', $role['permissions'])->get()
            );
        }
    }
} 