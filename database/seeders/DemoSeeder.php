<?php

namespace Database\Seeders;

use App\Demo\DemoWorld;
use App\Models\Company;
use App\Models\CompanyRequest;
use App\Models\CompanyType;
use App\Models\Permission;
use App\Models\PermitRequest;
use App\Models\PermitType;
use App\Models\PlotListing;
use App\Models\PortalFeature;
use App\Models\Role;
use App\Models\User;
use App\Support\Portal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * A filled-in portal for demo mode (PORTAL_DEMO=true): log in as
 * demo@openminetopia.nl / demo1234. The plugin data comes from DemoWorld.
 */
class DemoSeeder extends Seeder
{
    public const EMAIL = 'demo@openminetopia.nl';

    public const PASSWORD = 'demo1234';

    public function run(): void
    {
        if (! Portal::demo()) {
            throw new RuntimeException('Zet PORTAL_DEMO=true (en geen APP_ENV=production) om demodata te laden.');
        }

        PortalFeature::query()->update(['is_enabled' => true]);

        $admin = Role::where('is_admin', true)->firstOrFail();
        $player = Role::updateOrCreate(['slug' => 'player'], ['name' => 'Speler', 'description' => 'Iedere speler', 'is_admin' => false, 'is_game_role' => false]);
        $police = Role::updateOrCreate(['slug' => 'politie'], ['name' => 'Politie', 'description' => 'Agenten van Westerdam', 'is_admin' => false, 'is_game_role' => false]);
        $police->permissions()->syncWithoutDetaching(Permission::where('slug', 'manage-police')->pluck('id'));

        $users = [];
        foreach (DemoWorld::PLAYERS as $name => $uuid) {
            $users[$name] = User::updateOrCreate(['minecraft_username' => $name], [
                'name' => str_replace('_', ' ', $name),
                'email' => $name === 'Burgemeester' ? self::EMAIL : Str::lower($name).'@demo.openminetopia.nl',
                'password' => Hash::make(self::PASSWORD),
                'token' => Str::random(32),
                'minecraft_uuid' => DemoWorld::plain($uuid),
                'minecraft_plain_uuid' => $uuid,
                'minecraft_verified' => true,
                'minecraft_verified_at' => now()->subMonths(2),
            ]);
            $users[$name]->roles()->syncWithoutDetaching([$player->id]);
        }

        $users['Burgemeester']->roles()->syncWithoutDetaching([$admin->id]);
        $users['Agent_Sanne']->roles()->syncWithoutDetaching([$police->id]);

        $field = fn (string $label, string $type = 'text') => ['label' => $label, 'type' => $type, 'required' => true];

        $building = PermitType::updateOrCreate(['name' => 'Bouwvergunning'], [
            'slug' => 'bouwvergunning', 'description' => 'Voor nieuwbouw en verbouwingen binnen de gemeente.',
            'price' => 2500, 'is_active' => true, 'authorized_roles' => [$admin->id],
            'form_fields' => [$field('Plot'), $field('Wat ga je bouwen?', 'textarea')],
        ]);
        PermitType::updateOrCreate(['name' => 'Visvergunning'], [
            'slug' => 'visvergunning', 'description' => 'Vissen in de haven en de grachten.',
            'price' => 150, 'is_active' => true, 'authorized_roles' => [$police->id],
            'form_fields' => [$field('Waar wil je vissen?')],
        ]);
        PermitType::updateOrCreate(['name' => 'Evenementenvergunning'], [
            'slug' => 'evenementenvergunning', 'description' => 'Voor markten, feesten en optochten.',
            'price' => 750, 'is_active' => true, 'authorized_roles' => [$admin->id],
            'form_fields' => [$field('Naam van het evenement'), $field('Datum'), $field('Beschrijving', 'textarea')],
        ]);

        PermitRequest::updateOrCreate(['user_id' => $users['Lotte']->id, 'permit_type_id' => $building->id], [
            'form_data' => ['Plot' => 'westerdam_bakkerij', 'Wat ga je bouwen?' => 'Een terras aan de achterkant van de bakkerij.'],
            'status' => 'pending', 'price' => 2500, 'bank_account_uuid' => 'a1f0c9e2-0000-4000-8000-000000000004',
        ]);

        $soleTrader = CompanyType::updateOrCreate(['name' => 'Eenmanszaak'], [
            'description' => 'Voor ondernemers die alleen werken.',
            'price' => 1000, 'is_active' => true, 'authorized_roles' => [$admin->id],
            'form_fields' => [$field('Wat doet je bedrijf?', 'textarea')],
        ]);
        CompanyType::updateOrCreate(['name' => 'Besloten vennootschap'], [
            'description' => 'Voor grotere bedrijven met personeel.',
            'price' => 5000, 'is_active' => true, 'authorized_roles' => [$admin->id],
            'form_fields' => [$field('Wat doet je bedrijf?', 'textarea'), $field('Aantal medewerkers')],
        ]);

        $bakery = CompanyRequest::updateOrCreate(['name' => 'Bakkerij Lotte'], [
            'company_type_id' => $soleTrader->id, 'user_id' => $users['Lotte']->id,
            'form_data' => ['Wat doet je bedrijf?' => 'Brood, taart en koffie aan de Markt.'],
            'status' => 'approved', 'handled_by' => $users['Burgemeester']->id, 'handled_at' => now()->subWeeks(3), 'price' => 1000,
            'bank_account_uuid' => 'a1f0c9e2-0000-4000-8000-000000000003',
        ]);
        Company::updateOrCreate(['name' => 'Bakkerij Lotte'], [
            'slug' => 'bakkerij-lotte', 'type_id' => $soleTrader->id, 'owner_id' => $users['Lotte']->id,
            'kvk_number' => '10482733', 'description' => 'Brood, taart en koffie aan de Markt.',
            'is_active' => true, 'company_request_id' => $bakery->id,
        ]);
        CompanyRequest::updateOrCreate(['name' => 'Havenbedrijf Daan'], [
            'company_type_id' => $soleTrader->id, 'user_id' => $users['Daan']->id,
            'form_data' => ['Wat doet je bedrijf?' => 'Laden en lossen in Noordhaven.'],
            'status' => 'pending', 'price' => 1000, 'bank_account_uuid' => 'a1f0c9e2-0000-4000-8000-000000000005',
        ]);

        PlotListing::updateOrCreate(['plot_name' => 'westerdam_kade_12'], [
            'seller_id' => $users['Burgemeester']->id,
            'payout_bank_account_uuid' => array_key_first(DemoWorld::ACCOUNTS),
            'price' => 85000, 'description' => 'Karakteristiek grachtenpand met uitzicht op de Kade. Drie verdiepingen en een kelder.',
            'min_x' => 240, 'min_y' => 64, 'min_z' => -280, 'max_x' => 252, 'max_y' => 95, 'max_z' => -268,
            'instant_buy' => true, 'status' => 'active',
        ]);
    }
}
