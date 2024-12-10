<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;
use App\Models\Role;
use App\Models\PortalFeature;

class CreateCriticalData extends Migration
{
    public function up(): void
    {
        // Create critical permissions
        $permissions = [
            ['name' => 'Agent', 'slug' => 'manage-police', 'group' => 'police'],
            ['name' => 'Bedrijven beheren', 'slug' => 'manage-companies', 'group' => 'companies'],
            ['name' => 'Vergunningen beheren', 'slug' => 'manage-permits', 'group' => 'permits'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Create admin role
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Beheerder',
                'description' => 'Volledige toegang tot alle features',
                'is_admin' => true,
                'is_game_role' => false,
            ]
        );

        // Give admin all permissions
        $adminRole->permissions()->sync(Permission::all());

        // Create portal features
        $features = [
            [
                'key' => 'companies',
                'name' => 'Bedrijven',
                'description' => 'Beheer bedrijven en hun eigenaren (KvK)',
                'is_enabled' => false
            ],
            [
                'key' => 'permits',
                'name' => 'Vergunningen',
                'description' => 'Schakel het vergunningen systeem in of uit.',
                'is_enabled' => false,
            ],
            [
                'key' => 'broker',
                'name' => 'Makelaar',
                'description' => 'Laat spelers plot kopen en verkopen.',
                'is_enabled' => false
            ],
        ];

        foreach ($features as $feature) {
            PortalFeature::updateOrCreate(
                ['key' => $feature['key']],
                $feature
            );
        }
    }

    public function down(): void
    {
        //
    }
}
