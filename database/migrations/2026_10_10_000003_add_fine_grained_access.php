<?php

use App\Support\Features;
use App\Support\Permissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Features for every part of the portal and a permission per player feature and
 * per management page. Nothing changes for existing portals: the parts that had
 * no switch start on, and every user gets a default role that may use them all.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_admin');
        });

        Features::sync();
        Permissions::sync();

        $role = DB::table('roles')->where('slug', 'player')->first()
            ?? DB::table('roles')->where('name', 'Speler')->first();

        $roleId = $role->id ?? DB::table('roles')->insertGetId([
            'name' => 'Speler',
            'slug' => 'player',
            'description' => 'Iedere speler krijgt deze rol automatisch.',
            'is_admin' => false,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('roles')->where('id', $roleId)->update(['is_default' => true]);

        $permissionIds = DB::table('permissions')->whereIn('slug', Permissions::inGroup(Permissions::PLAYER))->pluck('id');
        $has = DB::table('permission_role')->where('role_id', $roleId)->pluck('permission_id');
        DB::table('permission_role')->insert(
            $permissionIds->diff($has)->map(fn ($id) => ['permission_id' => $id, 'role_id' => $roleId])->values()->all()
        );

        $members = DB::table('role_user')->where('role_id', $roleId)->pluck('user_id');
        DB::table('users')->whereNotIn('id', $members)->pluck('id')->chunk(500)->each(function ($userIds) use ($roleId) {
            DB::table('role_user')->insert($userIds->map(fn ($id) => ['role_id' => $roleId, 'user_id' => $id])->values()->all());
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
