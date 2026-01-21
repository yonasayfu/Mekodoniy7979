<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorship_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('proposed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('frequency')->default('monthly');
            $table->string('relationship_type')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, declined, expired, cancelled
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['donor_id', 'status']);
            $table->index(['elder_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorship_proposals');
    }
};
