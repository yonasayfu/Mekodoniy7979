<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('payment_id');
            }

            if (!Schema::hasColumn('donations', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('elder_id')->constrained('branches')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('donations', 'branch_id') && Schema::hasTable('elders')) {
            $driver = config('database.default');

            if ($driver === 'mysql') {
                DB::statement("UPDATE donations d JOIN elders e ON e.id = d.elder_id SET d.branch_id = e.branch_id WHERE d.branch_id IS NULL AND d.elder_id IS NOT NULL");
            } else {
                DB::statement("UPDATE donations SET branch_id = e.branch_id FROM elders e WHERE e.id = donations.elder_id AND donations.branch_id IS NULL AND donations.elder_id IS NOT NULL");
            }
        }
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }

            if (Schema::hasColumn('donations', 'receipt_path')) {
                $table->dropColumn('receipt_path');
            }
        });
    }
};
