<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (! Schema::hasColumn('donations', 'receipt_uuid')) {
                $table->uuid('receipt_uuid')->nullable()->unique()->after('payment_id');
            }

            if (! Schema::hasColumn('donations', 'receipt_issued_at')) {
                $table->timestamp('receipt_issued_at')->nullable()->after('receipt_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'receipt_issued_at')) {
                $table->dropColumn('receipt_issued_at');
            }

            if (Schema::hasColumn('donations', 'receipt_uuid')) {
                $table->dropColumn('receipt_uuid');
            }
        });
    }
};
