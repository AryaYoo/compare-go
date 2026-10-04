<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('procurements')->cascadeOnDelete();
            $table->string('name');
            $table->json('images')->nullable();
            $table->json('ai_result')->nullable();
            $table->decimal('total_score', 5, 2)->nullable();
            $table->enum('ai_status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->text('ai_error')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
