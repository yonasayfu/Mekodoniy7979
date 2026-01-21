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
        Schema::table('sponsorships', function (Blueprint $table) {
            if (! Schema::hasColumn('sponsorships', 'subscription_gateway')) {
                $table->string('subscription_gateway')->nullable()->after('subscription_id');
            }

            if (! Schema::hasColumn('sponsorships', 'subscription_metadata')) {
                $table->json('subscription_metadata')->nullable()->after('subscription_gateway');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            if (Schema::hasColumn('sponsorships', 'subscription_metadata')) {
                $table->dropColumn('subscription_metadata');
            }

            if (Schema::hasColumn('sponsorships', 'subscription_gateway')) {
                $table->dropColumn('subscription_gateway');
            }
        });
    }
};
