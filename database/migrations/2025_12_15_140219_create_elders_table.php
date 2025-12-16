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
        Schema::create('elders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->string('priority_level')->default('medium'); // low, medium, high
            $table->text('health_status')->nullable();
            $table->text('special_needs')->nullable();
            $table->decimal('monthly_expenses', 8, 2)->nullable();
            $table->string('video_url')->nullable();
            $table->text('health_conditions')->nullable();
            $table->string('sponsorship_status')->default('not_sponsored');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elders');
    }
};
