<?php

use App\Models\Permission;
use App\Models\PermitType;
use App\Models\PortalFeature;
use App\Models\Role;
use App\Models\User;
use App\Support\Features;
use App\Support\Permissions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

beforeEach(function () {
    // No Minecraft server in tests: plugin calls answer empty.
    Http::fake();
});

function player(array $roles = []): User
{
    $user = User::create([
        'name' => 'Speler '.Str::random(4), 'email' => Str::random(8).'@example.com', 'minecraft_username' => 'Speler'.Str::random(4),
        'password' => Hash::make('geheim123'), 'token' => Str::random(32),
        'minecraft_verified' => true, 'minecraft_uuid' => (string) Str::uuid(), 'minecraft_plain_uuid' => (string) Str::uuid(),
    ]);

    $user->roles()->syncWithoutDetaching(collect($roles)->pluck('id'));

    return $user->fresh();
}

function roleWith(string ...$permissions): Role
{
    $role = Role::create(['name' => 'Rol '.Str::random(4), 'slug' => Str::random(8)]);
    $role->permissions()->sync(Permission::whereIn('slug', $permissions)->pluck('id'));

    return $role;
}

function turnOn(string ...$features): void
{
    PortalFeature::whereIn('key', $features)->update(['is_enabled' => true]);
    Features::forget();
}

it('keeps what was always there on and gives every user the default role', function () {
    expect(Features::enabled('bank'))->toBeTrue()
        ->and(Features::enabled('plots'))->toBeTrue()
        ->and(Features::enabled('criminal_records'))->toBeTrue()
        ->and(Features::enabled('police'))->toBeTrue()
        ->and(Features::enabled('broker'))->toBeFalse();

    $default = Role::where('is_default', true)->sole();
    expect($default->slug)->toBe('player')
        ->and($default->permissions->pluck('slug')->sort()->values()->all())
        ->toBe(collect(Permissions::inGroup(Permissions::PLAYER))->sort()->values()->all());

    $user = player();
    expect($user->roles->pluck('id')->all())->toBe([$default->id])
        ->and($user->can('use-bank'))->toBeTrue()
        ->and($user->can('manage-settings'))->toBeFalse();
});

it('hides a feature that is off, also for admins', function () {
    $admin = player([Role::where('slug', 'admin')->first()]);

    $this->actingAs($admin)->get('http://portaal.test/plots/te-koop')->assertNotFound();

    turnOn('broker');
    $this->actingAs($admin)->get('http://portaal.test/plots/te-koop')->assertOk();
});

it('needs a feature that the feature depends on', function () {
    turnOn('broker');
    PortalFeature::where('key', 'plots')->update(['is_enabled' => false]);
    Features::forget();

    expect(Features::enabled('broker'))->toBeFalse();
    $this->actingAs(player())->get('http://portaal.test/plots/te-koop')->assertNotFound();
});

it('lets a player use a feature only with its permission', function () {
    turnOn('broker');
    $user = player();
    $this->actingAs($user)->get('http://portaal.test/plots/te-koop')->assertOk()->assertSee('Makelaar');

    Role::where('slug', 'player')->first()->permissions()->detach(Permission::where('slug', 'use-broker')->value('id'));

    $this->actingAs($user->fresh())->get('http://portaal.test/plots/te-koop')->assertForbidden();
    $this->actingAs($user->fresh())->get('http://portaal.test/')->assertOk()->assertDontSee('Makelaar');
});

it('opens only the management pages a role has a permission for', function () {
    $user = player([roleWith('manage-settings')]);

    $this->actingAs($user)->get('http://portaal.test/portal/admin/settings')->assertOk();
    $this->actingAs($user)->get('http://portaal.test/portal/admin/users')->assertForbidden();
    $this->actingAs($user)->get('http://portaal.test/portal/admin/roles')->assertForbidden();
    $this->actingAs($user)->get('http://portaal.test/portal/admin/appearance')->assertForbidden();
});

it('does not let a role manager hand out permissions or admin they do not have', function () {
    $user = player([roleWith('manage-roles')]);

    $this->actingAs($user)->post('http://portaal.test/portal/admin/roles', [
        'name' => 'Instellers', 'slug' => 'instellers',
        'permissions' => [Permission::where('slug', 'manage-settings')->value('id')],
    ])->assertSessionHasErrors('permissions.0');

    $this->actingAs($user)->post('http://portaal.test/portal/admin/roles', [
        'name' => 'Bankiers', 'slug' => 'bankiers', 'is_admin' => '1',
        'permissions' => [Permission::where('slug', 'use-bank')->value('id')],
    ])->assertRedirect();

    $role = Role::where('slug', 'bankiers')->sole();
    expect($role->is_admin)->toBeFalse()
        ->and($role->permissions->pluck('slug')->all())->toBe(['use-bank']);

    $admin = Role::where('slug', 'admin')->first();
    $this->actingAs($user)->get("http://portaal.test/portal/admin/roles/{$admin->id}/edit")->assertForbidden();
    $this->actingAs($user)->delete("http://portaal.test/portal/admin/roles/{$admin->id}");
    expect(Role::whereKey($admin->id)->exists())->toBeTrue();
});

it('does not let a user manager make someone admin', function () {
    $manager = player([roleWith('manage-users')]);
    $other = player();
    $admin = Role::where('slug', 'admin')->first();

    $this->actingAs($manager)->post("http://portaal.test/portal/admin/users/{$other->id}/roles", ['roles' => [$admin->id]])
        ->assertSessionHas('error');

    expect($other->fresh()->isAdmin())->toBeFalse();
});

it('reaches company management at /portal/companies/manage', function () {
    turnOn('companies');
    $user = player([roleWith('manage-companies')]);

    $this->actingAs($user)->get('http://portaal.test/portal/companies/manage')
        ->assertOk()
        ->assertViewIs('portal.companies.manage.index');
});

it('limits a permit type with roles to those roles', function () {
    turnOn('permits');
    $gemeente = roleWith('manage-permits');
    $politie = roleWith('manage-permits');
    $type = fn (array $roles) => PermitType::create([
        'name' => 'Type '.Str::random(4), 'slug' => Str::random(8), 'form_fields' => [], 'authorized_roles' => $roles,
    ]);

    $forGemeente = $type([(string) $gemeente->id]);
    $forEveryManager = $type([]);

    $ambtenaar = player([$gemeente]);
    $agent = player([$politie]);
    $speler = player();

    expect($forGemeente->userCanManage($ambtenaar))->toBeTrue()
        ->and($forGemeente->userCanManage($agent))->toBeFalse()
        ->and($forEveryManager->userCanManage($agent))->toBeTrue()
        ->and($forEveryManager->userCanManage($speler))->toBeFalse()
        ->and(PermitType::manageableIdsFor($agent))->toBe([$forEveryManager->id]);
});
