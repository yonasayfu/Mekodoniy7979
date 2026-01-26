<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (! Schema::hasColumn('donations', 'cadence')) {
                $table->string('cadence')->nullable()->after('donation_type');
            }

            if (! Schema::hasColumn('donations', 'recurrence_duration')) {
                $table->unsignedSmallInteger('recurrence_duration')->nullable()->after('cadence');
            }

            if (! Schema::hasColumn('donations', 'deduction_schedule')) {
                $table->string('deduction_schedule')->nullable()->after('recurrence_duration');
            }

            if (! Schema::hasColumn('donations', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('status');
            }

            if (! Schema::hasColumn('donations', 'mandate_path')) {
                $table->string('mandate_path')->nullable()->after('receipt_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'mandate_path')) {
                $table->dropColumn('mandate_path');
            }

            if (Schema::hasColumn('donations', 'payment_status')) {
                $table->dropColumn('payment_status');
            }

            if (Schema::hasColumn('donations', 'deduction_schedule')) {
                $table->dropColumn('deduction_schedule');
            }

            if (Schema::hasColumn('donations', 'recurrence_duration')) {
                $table->dropColumn('recurrence_duration');
            }

            if (Schema::hasColumn('donations', 'cadence')) {
                $table->dropColumn('cadence');
            }
        });
    }
};
