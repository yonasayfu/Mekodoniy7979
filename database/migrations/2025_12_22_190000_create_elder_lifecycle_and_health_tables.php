<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elder_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_id')->constrained('elders')->cascadeOnDelete();
            $table->string('from_status');
            $table->string('to_status');
            $table->text('reason')->nullable();
            $table->timestamp('occurred_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['elder_id', 'occurred_at']);
        });

        Schema::create('elder_health_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_id')->constrained('elders')->cascadeOnDelete();
            $table->date('assessment_date');
            $table->text('summary');
            $table->string('mobility_level')->nullable();
            $table->string('risk_level')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['elder_id', 'assessment_date']);
        });

        Schema::create('elder_medical_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_id')->constrained('elders')->cascadeOnDelete();
            $table->string('condition_name');
            $table->date('diagnosed_at')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['elder_id', 'status']);
        });

        Schema::create('elder_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_id')->constrained('elders')->cascadeOnDelete();
            $table->string('medication_name');
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['elder_id', 'ended_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elder_medications');
        Schema::dropIfExists('elder_medical_conditions');
        Schema::dropIfExists('elder_health_assessments');
        Schema::dropIfExists('elder_status_events');
    }
};
