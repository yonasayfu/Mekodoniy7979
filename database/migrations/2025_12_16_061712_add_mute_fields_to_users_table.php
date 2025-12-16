<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('muted_at')->nullable()->after('ban_reason');
            $table->timestamp('muted_until')->nullable()->after('muted_at');
            $table->text('mute_reason')->nullable()->after('muted_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['muted_at', 'muted_until', 'mute_reason']);
        });
    }
};
