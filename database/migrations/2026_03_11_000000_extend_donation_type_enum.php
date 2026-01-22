<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('donations')) {
            return;
        }

        DB::statement('ALTER TABLE donations DROP CONSTRAINT IF EXISTS donations_donation_type_check');
        DB::statement("ALTER TABLE donations ALTER COLUMN donation_type TYPE varchar(255)");
        DB::statement("ALTER TABLE donations ADD CONSTRAINT donations_donation_type_check CHECK (donation_type IN ('pledge','guest_one_time','guest_meal','guest_sponsorship'))");
    }

    public function down(): void
    {
        if (! Schema::hasTable('donations')) {
            return;
        }

        DB::statement('ALTER TABLE donations DROP CONSTRAINT IF EXISTS donations_donation_type_check');
        DB::statement("ALTER TABLE donations ALTER COLUMN donation_type TYPE varchar(255)");
        DB::statement("ALTER TABLE donations ADD CONSTRAINT donations_donation_type_check CHECK (donation_type IN ('pledge','guest_one_time','guest_meal'))");
    }
};
