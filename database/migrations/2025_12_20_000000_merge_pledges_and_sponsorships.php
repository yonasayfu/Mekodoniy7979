<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create a new table with the desired final schema
        Schema::create('sponsorships_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('elder_id');
            $table->decimal('amount', 8, 2);
            $table->string('currency')->default('ETB');
            $table->string('frequency')->default('monthly');
            $table->string('relationship_type');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->string('subscription_id')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->boolean('promise_kept_last_month')->default(false);
            $table->integer('consecutive_months_kept')->default(0);
            $table->integer('missed_payment_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('elder_id')->references('id')->on('elders')->onDelete('cascade');
        });

        // 2. Copy data from the old `sponsorships` table
        if (Schema::hasTable('sponsorships')) {
            DB::table('sponsorships')->orderBy('id')->chunk(100, function ($chunk) {
                $data = $chunk->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'user_id' => $item->user_id,
                        'elder_id' => $item->elder_id,
                        'amount' => $item->amount,
                        'currency' => 'ETB', // Default currency
                        'frequency' => 'monthly', // Default frequency
                        'relationship_type' => 'friend', // Default relationship type
                        'start_date' => $item->start_date ?? now(),
                        'end_date' => $item->end_date,
                        'status' => $item->status,
                        'notes' => $item->notes,
                        'subscription_id' => null, // Not present in old table
                        'next_billing_date' => null, // Not present in old table
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                })->toArray();
                DB::table('sponsorships_new')->insert($data);
            });
        }

        // 3. Copy data from the `pledges` table
        if (Schema::hasTable('pledges')) {
            DB::table('pledges')->orderBy('id')->chunk(100, function ($chunk) {
                $data = $chunk->map(function ($item) {
                    return [
                        // Assuming pledges IDs might conflict, we don't copy pledge ID directly
                        'user_id' => $item->user_id,
                        'elder_id' => $item->elder_id,
                        'amount' => $item->amount,
                        'currency' => $item->currency ?? 'ETB',
                        'frequency' => $item->frequency ?? 'monthly',
                        'relationship_type' => $item->relationship_type ?? 'friend',
                        'start_date' => $item->start_date ?? now(),
                        'end_date' => $item->end_date,
                        'status' => $item->status,
                        'notes' => $item->notes,
                        'subscription_id' => $item->subscription_id,
                        'next_billing_date' => $item->next_billing_date,
                        'promise_kept_last_month' => $item->promise_kept_last_month ?? false,
                        'consecutive_months_kept' => $item->consecutive_months_kept ?? 0,
                        'missed_payment_count' => $item->missed_payment_count ?? 0,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                })->toArray();
                DB::table('sponsorships_new')->insert($data);
            });
        }
        
        // 4. Update the donations table to point to the new sponsorships table
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'pledge_id')) {
                $table->renameColumn('pledge_id', 'sponsorship_id');
            }
        });


        // 5. Drop old tables
        Schema::dropIfExists('pledges');
        Schema::dropIfExists('sponsorships');

        // 6. Rename the new table
        Schema::rename('sponsorships_new', 'sponsorships');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This down method is not perfect, but it's a good-faith effort to restore the old state.
        Schema::dropIfExists('sponsorships');
        
        // You might need to recreate the old pledges and sponsorships tables if you want a perfect rollback.
        // For the sake of this migration, we'll just drop the new table.
    }
};