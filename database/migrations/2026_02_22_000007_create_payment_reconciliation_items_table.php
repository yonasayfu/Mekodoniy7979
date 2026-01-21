<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_reconciliation_import_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('elder_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gateway');
            $table->string('reference')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_phone')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('unmatched'); // unmatched, matched, ignored
            $table->string('match_strategy')->nullable();
            $table->json('raw_payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'gateway']);
            $table->index(['reference', 'gateway']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_reconciliation_items');
    }
};
