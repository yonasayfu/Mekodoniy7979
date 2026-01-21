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
        if (! Schema::hasTable('sponsorships')) {
            return;
        }

        Schema::table('sponsorships', function (Blueprint $table) {
            if (! Schema::hasColumn('sponsorships', 'branch_id')) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->after('elder_id')
                    ->constrained()
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('sponsorships')) {
            return;
        }

        Schema::table('sponsorships', function (Blueprint $table) {
            if (Schema::hasColumn('sponsorships', 'branch_id')) {
                $table->dropConstrainedForeignId('branch_id');
            }
        });
    }
};
