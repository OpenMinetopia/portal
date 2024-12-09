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
                'key' => 'permits',
                'name' => 'Vergunningen',
                'description' => 'Beheer vergunningen voor spelers en bedrijven',
                'is_enabled' => false
            ],
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
                'is_enabled' => true,
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