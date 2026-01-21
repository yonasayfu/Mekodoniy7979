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
        Schema::table('visits', function (Blueprint $table) {
            $table->boolean('needs_translator')
                ->default(false)
                ->after('status');
            $table->string('translator_language')
                ->nullable()
                ->after('needs_translator');
            $table->boolean('needs_transport')
                ->default(false)
                ->after('translator_language');
            $table->string('transport_preference')
                ->nullable()
                ->after('needs_transport');
            $table->text('logistics_notes')
                ->nullable()
                ->after('transport_preference');

            $table->dateTime('approval_deadline')
                ->nullable()
                ->after('logistics_notes');
            $table->dateTime('approved_at')
                ->nullable()
                ->after('approval_deadline');
            $table->dateTime('sla_reminder_sent_at')
                ->nullable()
                ->after('approved_at');
            $table->dateTime('sla_breached_notified_at')
                ->nullable()
                ->after('sla_reminder_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'needs_translator',
                'translator_language',
                'needs_transport',
                'transport_preference',
                'logistics_notes',
                'approval_deadline',
                'approved_at',
                'sla_reminder_sent_at',
                'sla_breached_notified_at',
            ]);
        });
    }
};
