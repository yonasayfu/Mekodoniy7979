<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elder_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('other');
            $table->string('label')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->index(['elder_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elder_documents');
    }
};
