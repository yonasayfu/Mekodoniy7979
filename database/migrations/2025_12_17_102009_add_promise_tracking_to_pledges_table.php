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
        Schema::table('pledges', function (Blueprint $table) {
            $table->enum('relationship_type', ['father', 'mother', 'brother', 'sister'])->nullable()->after('status');
            $table->boolean('promise_kept_last_month')->default(true)->after('relationship_type');
            $table->integer('consecutive_months_kept')->default(0)->after('promise_kept_last_month');
            $table->date('last_payment_reminder')->nullable()->after('consecutive_months_kept');
            $table->integer('missed_payment_count')->default(0)->after('last_payment_reminder');
        });
    }

    public function down(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->dropColumn(['relationship_type', 'promise_kept_last_month', 'consecutive_months_kept', 'last_payment_reminder', 'missed_payment_count']);
        });
    }
};
