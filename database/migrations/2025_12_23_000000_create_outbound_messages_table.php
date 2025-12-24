<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outbound_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('elder_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel'); // email, sms, whatsapp, etc.
            $table->string('to'); // email address or phone number
            $table->string('subject')->nullable(); // for email
            $table->text('content');
            $table->string('template')->nullable(); // template identifier
            $table->json('template_data')->nullable(); // template variables
            $table->enum('status', [
                'pending',
                'sending',
                'sent',
                'delivered',
                'failed',
                'bounced'
            ])->default('pending');
            $table->string('external_id')->nullable(); // ID from email/SMS provider
            $table->text('error_message')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['channel', 'status']);
            $table->index('user_id');
            $table->index('elder_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('outbound_messages');
    }
};
