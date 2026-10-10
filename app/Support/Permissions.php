<?php

namespace App\Support;

use App\Models\Permission;

/**
 * Every permission a role can have. Each one belongs to a feature: when that
 * feature is off, nobody has the permission, admins included.
 */
final class Permissions
{
    public const PLAYER = 'player';

    public const MANAGE = 'manage';

    public const GROUPS = [
        self::PLAYER => 'Spelers',
        self::MANAGE => 'Beheer',
    ];

    /** slug => [name, description, group, feature] */
    public const ALL = [
        'use-bank' => ['Bankrekeningen', 'Eigen bankrekeningen en saldo bekijken.', self::PLAYER, 'bank'],
        'use-transactions' => ['Overboeken', 'Geld overmaken naar andere rekeningen.', self::PLAYER, 'transactions'],
        'use-plots' => ['Plots', 'Eigen plots bekijken en eigenaren en leden beheren.', self::PLAYER, 'plots'],
        'use-broker' => ['Makelaar', 'Plots kopen en eigen plots verkopen.', self::PLAYER, 'broker'],
        'view-criminal-record' => ['Strafblad', 'Eigen strafblad bekijken.', self::PLAYER, 'criminal_records'],
        'request-permits' => ['Vergunningen aanvragen', 'Vergunningen aanvragen en eigen aanvragen bekijken.', self::PLAYER, 'permits'],
        'use-companies' => ['Bedrijven', 'Bedrijven aanvragen, beheren en opheffen.', self::PLAYER, 'companies'],
        'view-company-registry' => ['Bedrijvenregister', 'Het bedrijvenregister doorzoeken.', self::PLAYER, 'companies'],

        'manage-users' => ['Gebruikers beheren', 'Gebruikers bekijken en bewerken en hun rollen toewijzen.', self::MANAGE, null],
        'manage-roles' => ['Rollen beheren', 'Rollen aanmaken, bewerken en verwijderen.', self::MANAGE, null],
        'manage-settings' => ['Instellingen', 'Features aan- en uitzetten en de instellingen van vergunningen en bedrijven.', self::MANAGE, null],
        'manage-appearance' => ['Uiterlijk', 'De kleur en het logo van het portaal aanpassen.', self::MANAGE, null],
        'manage-plots' => ['Plots beheren', 'Alle plots bekijken en eigenaren en leden beheren.', self::MANAGE, 'plots'],
        'manage-broker' => ['Makelaar beheren', 'Plots namens de server te koop zetten en listings van de markt halen.', self::MANAGE, 'broker'],
        'manage-permits' => ['Vergunningaanvragen', 'Vergunningaanvragen behandelen.', self::MANAGE, 'permits'],
        'manage-permit-types' => ['Vergunningtypes', 'Vergunningtypes aanmaken en bewerken.', self::MANAGE, 'permits'],
        'manage-companies' => ['Bedrijfsaanvragen', 'Bedrijfs- en opheffingsaanvragen behandelen.', self::MANAGE, 'companies'],
        'manage-company-types' => ['Bedrijfstypes', 'Bedrijfstypes aanmaken en bewerken.', self::MANAGE, 'companies'],
        'manage-police' => ['Politie', 'De spelersdatabase gebruiken.', self::MANAGE, 'police'],
    ];

    public static function exists(string $slug): bool
    {
        return isset(self::ALL[$slug]);
    }

    public static function feature(string $slug): ?string
    {
        return self::ALL[$slug][3] ?? null;
    }

    /** @return list<string> */
    public static function inGroup(string $group): array
    {
        return array_keys(array_filter(self::ALL, fn ($permission) => $permission[2] === $group));
    }

    /** Adds missing permissions and keeps names, descriptions and groups current. */
    public static function sync(): void
    {
        foreach (self::ALL as $slug => [$name, $description, $group]) {
            Permission::updateOrCreate(['slug' => $slug], ['name' => $name, 'description' => $description, 'group' => $group]);
        }
    }
}
