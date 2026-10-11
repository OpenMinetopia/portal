<?php

namespace App\Support;

use App\Models\PortalFeature;
use Illuminate\Support\Collection;

/**
 * Every part of the portal an admin can switch on or off. The rows live in
 * portal_features (one per portal); this is the list they are made from.
 */
final class Features
{
    /** key => [name, description, the feature it needs, on for a new portal] */
    public const ALL = [
        'bank' => ['Bankrekeningen', 'Spelers zien hun bankrekeningen en saldo.', null, true],
        'transactions' => ['Overboekingen', 'Spelers maken geld over naar andere rekeningen.', 'bank', false],
        'plots' => ['Plots', 'Spelers zien hun plots en beheren eigenaren en leden.', null, true],
        'broker' => ['Makelaar', 'Plots kopen, verkopen, huren en verhuren.', 'plots', false],
        'criminal_records' => ['Strafblad', 'Spelers zien hun eigen strafblad.', null, true],
        'permits' => ['Vergunningen', 'Vergunningen aanvragen en behandelen.', null, false],
        'companies' => ['Bedrijven', 'Bedrijven aanvragen, het bedrijvenregister en de KvK.', null, false],
        'police' => ['Politie', "De spelersdatabase voor agenten, die er ook VOG's afgeven.", null, true],
    ];

    public static function name(string $key): string
    {
        return self::ALL[$key][0] ?? $key;
    }

    public static function requires(string $key): ?string
    {
        return self::ALL[$key][2] ?? null;
    }

    /** Whether the feature and the feature it needs are both on. */
    public static function enabled(string $key): bool
    {
        $enabled = self::enabledKeys();

        while ($key !== null) {
            if (! $enabled->contains($key)) {
                return false;
            }

            $key = self::requires($key);
        }

        return true;
    }

    /** Read once per request; PortalFeature forgets it when a row changes. */
    public static function enabledKeys(): Collection
    {
        $attributes = request()->attributes;

        if (! $attributes->has('portal.features')) {
            $attributes->set('portal.features', PortalFeature::where('is_enabled', true)->pluck('key'));
        }

        return $attributes->get('portal.features');
    }

    public static function forget(): void
    {
        request()->attributes->remove('portal.features');
    }

    /** Adds missing features and keeps names and descriptions current; never touches is_enabled of existing rows. */
    public static function sync(): void
    {
        foreach (self::ALL as $key => [$name, $description, , $default]) {
            $feature = PortalFeature::firstOrNew(['key' => $key]);
            $feature->fill(['name' => $name, 'description' => $description]);

            if (! $feature->exists) {
                $feature->is_enabled = $default;
            }

            $feature->save();
        }

        self::forget();
    }
}
