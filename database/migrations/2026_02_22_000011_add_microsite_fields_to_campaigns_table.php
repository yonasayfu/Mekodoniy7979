<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('description');
            $table->string('cta_label')->nullable()->after('short_description');
            $table->string('cta_url')->nullable()->after('cta_label');
            $table->string('accent_color', 20)->nullable()->after('cta_url');
            $table->string('hero_image_path')->nullable()->after('accent_color');
            $table->string('featured_video_url')->nullable()->after('hero_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'short_description',
                'cta_label',
                'cta_url',
                'accent_color',
                'hero_image_path',
                'featured_video_url',
            ]);
        });
    }
};
