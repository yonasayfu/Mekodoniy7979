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
        // 1. Daily Stats Snapshot (For fast charts and historical analysis)
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete(); // Null = Global/Headquarters
            $table->date('date')->index();
            
            // Financials
            $table->decimal('total_pledged', 15, 2)->default(0); // Expected
            $table->decimal('total_collected', 15, 2)->default(0); // Actual
            $table->decimal('gap', 15, 2)->default(0); // Pledged - Collected
            
            // Operational
            $table->integer('active_elders')->default(0);
            $table->integer('matched_elders')->default(0); // Elders with sponsors
            $table->integer('active_donors')->default(0);
            
            $table->timestamps();
            
            // Ensure one record per branch per day
            $table->unique(['branch_id', 'date']);
        });

        // 2. Impact Metrics (To translate money into tangible help)
        Schema::create('impact_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Breakfast", "Medical Checkup"
            $table->decimal('unit_cost', 10, 2); // e.g., 70.00
            $table->string('icon')->nullable(); // FontAwesome class or SVG path
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_metrics');
        Schema::dropIfExists('daily_stats');
    }
};