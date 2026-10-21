<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->string('neutrals', 20)->nullable()->after('accent_color');
            $table->string('font', 20)->nullable()->after('neutrals');
            $table->string('heading_font', 20)->nullable()->after('font');
            $table->string('radius', 20)->nullable()->after('heading_font');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->dropColumn(['neutrals', 'font', 'heading_font', 'radius']);
        });
    }
};
