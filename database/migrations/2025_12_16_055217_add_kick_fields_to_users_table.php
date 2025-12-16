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
            $table->timestamp('kicked_at')->nullable()->after('approved_by');
            $table->timestamp('kicked_until')->nullable()->after('kicked_at');
            $table->text('kick_reason')->nullable()->after('kicked_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kicked_at', 'kicked_until', 'kick_reason']);
        });
    }
};
