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
        // To avoid issues with foreign keys, we drop them first and recreate them later.
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'pledge_id')) {
                $table->dropForeign(['pledge_id']);
                $table->dropColumn('pledge_id');
            }
        });

        // Drop pledges and sponsorships tables if they exist
        Schema::dropIfExists('pledges');
        Schema::dropIfExists('sponsorships');

        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('elder_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('frequency')->nullable();
            $table->string('relationship_type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->string('subscription_id')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->timestamps();
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('sponsorship_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['sponsorship_id']);
            $table->dropColumn('sponsorship_id');
        });

        Schema::dropIfExists('sponsorships');

        // Recreating old tables is complex, for this refactor we assume forward migration.
        // If you need to rollback, you would restore from a backup or write specific logic
        // to split sponsorships back into pledges and sponsorships.
        // For simplicity here, we'll recreate the original tables as empty.

        Schema::create('pledges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('elder_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('frequency')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->string('subscription_id')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->timestamps();
        });

        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('elder_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('frequency')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('pledge_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }
};
