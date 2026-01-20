<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pre_sponsorships', function (Blueprint $table) {
            $table->string('relationship_type')
                ->default('father')
                ->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_sponsorships', function (Blueprint $table) {
            $table->dropColumn('relationship_type');
        });
    }
};
