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
        Schema::table('elders', function (Blueprint $table) {
            $table->enum('relationship_type', ['father', 'mother', 'brother', 'sister'])->nullable()->after('sponsorship_status');
            $table->integer('relationship_priority')->default(4)->after('relationship_type'); // 1=highest (father), 4=lowest (sister)
            $table->boolean('is_featured')->default(false)->after('relationship_priority'); // For slideshow
            $table->json('monthly_expenses_breakdown')->nullable()->after('monthly_expenses'); // Detailed expense categories
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->dropColumn(['relationship_type', 'relationship_priority', 'is_featured', 'monthly_expenses_breakdown']);
        });
    }
};
