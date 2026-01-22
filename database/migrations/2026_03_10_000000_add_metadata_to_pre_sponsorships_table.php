<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_sponsorships', function (Blueprint $table) {
            $table->unsignedBigInteger('donation_id')->nullable()->after('relationship_type');
            $table->foreign('donation_id')->references('id')->on('donations')->cascadeOnDelete();
            $table->unsignedBigInteger('elder_id')->nullable()->after('donation_id');
            $table->foreign('elder_id')->references('id')->on('elders')->nullOnDelete();
            $table->unsignedBigInteger('branch_id')->nullable()->after('elder_id');
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->decimal('amount', 10, 2)->default(0)->after('branch_id');
            $table->string('currency', 3)->default('ETB')->after('amount');
            $table->string('status')->default('pending')->after('currency');
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('pre_sponsorships', function (Blueprint $table) {
            $table->dropForeign(['donation_id']);
            $table->dropForeign(['elder_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn([
                'notes',
                'status',
                'currency',
                'amount',
                'branch_id',
                'elder_id',
                'donation_id',
            ]);
        });
    }
};
