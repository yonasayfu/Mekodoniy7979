<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->boolean('kyc_required')->default(false)->after('notes');
            $table->string('kyc_status')->default('not_required')->after('kyc_required');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_status');
            $table->string('kyc_document_path')->nullable()->after('kyc_verified_at');
            $table->text('kyc_review_notes')->nullable()->after('kyc_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_review_notes',
                'kyc_document_path',
                'kyc_verified_at',
                'kyc_status',
                'kyc_required',
            ]);
        });
    }
};
