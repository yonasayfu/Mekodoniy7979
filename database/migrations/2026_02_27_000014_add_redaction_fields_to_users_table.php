<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('redaction_requested_at')->nullable()->after('muted_until');
            $table->timestamp('data_redacted_at')->nullable()->after('redaction_requested_at');
            $table->text('redaction_reason')->nullable()->after('data_redacted_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['redaction_reason', 'data_redacted_at', 'redaction_requested_at']);
        });
    }
};
