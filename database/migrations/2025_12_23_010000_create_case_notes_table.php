<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('case_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->enum('visibility', ['internal', 'donor_visible'])->default('internal');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['elder_id', 'created_at']);
            $table->index(['branch_id', 'created_at']);
            $table->index('created_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('case_notes');
    }
};
