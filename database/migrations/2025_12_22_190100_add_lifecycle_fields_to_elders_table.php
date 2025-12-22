<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->string('current_status')->default('available')->after('sponsorship_status');
            $table->timestamp('admitted_at')->nullable()->after('current_status');
            $table->timestamp('deceased_at')->nullable()->after('admitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->dropColumn(['current_status', 'admitted_at', 'deceased_at']);
        });
    }
};
