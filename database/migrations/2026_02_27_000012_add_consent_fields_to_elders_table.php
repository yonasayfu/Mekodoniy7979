<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->timestamp('consent_received_at')->nullable()->after('video_url');
            $table->string('consent_form_path')->nullable()->after('consent_received_at');
            $table->text('consent_notes')->nullable()->after('consent_form_path');
        });
    }

    public function down(): void
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->dropColumn(['consent_notes', 'consent_form_path', 'consent_received_at']);
        });
    }
};
