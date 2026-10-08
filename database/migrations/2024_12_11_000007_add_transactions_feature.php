<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PortalFeature;

return new class extends Migration
{
    public function up(): void
    {
        PortalFeature::create([
            'key' => 'transactions',
            'name' => 'Banktransacties',
            'description' => 'Laat mensen geld overmaken naar elkaar via het portaal',
            'is_enabled' => false
        ]);
    }

    public function down(): void
    {
        PortalFeature::where('key', 'transactions')->delete();
    }
}; 