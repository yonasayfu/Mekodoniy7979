<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elders', function (Blueprint $table) {
            if (! Schema::hasColumn('elders', 'funding_goal')) {
                $table->unsignedInteger('funding_goal')->default(0)->after('monthly_expenses');
            }

            if (! Schema::hasColumn('elders', 'funding_received')) {
                $table->unsignedInteger('funding_received')->default(0)->after('funding_goal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('elders', function (Blueprint $table) {
            if (Schema::hasColumn('elders', 'funding_received')) {
                $table->dropColumn('funding_received');
            }

            if (Schema::hasColumn('elders', 'funding_goal')) {
                $table->dropColumn('funding_goal');
            }
        });
    }
};
