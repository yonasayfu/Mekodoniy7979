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
        Schema::create('donor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('relationship_goal')->nullable();
            $table->decimal('monthly_budget', 10, 2)->nullable();
            $table->string('frequency')->default('monthly');
            $table->string('preferred_contact_method')->nullable();
            $table->json('contact_channels')->nullable();
            $table->string('payment_preference')->nullable();
            $table->text('notes')->nullable();
            $table->string('onboarding_step')->default('relationship');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_profiles');
    }
};
