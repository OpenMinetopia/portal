<?php

namespace Database\Seeders;

use App\Models\PortalFeature;
use Illuminate\Database\Seeder;

class PortalFeaturesSeeder extends Seeder
{
    public function run(): void
    {
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
}
