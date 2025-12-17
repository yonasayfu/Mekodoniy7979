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
        Schema::create('annual_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->year('report_year');
            $table->json('impact_data');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'report_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_reports');
    }
};
