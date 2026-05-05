<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->text('summary')->nullable();
            $table->json('keywords')->nullable();
            $table->string('sentiment')->nullable();       // positive / negative / neutral
            $table->float('sentiment_score')->nullable();  // 0.0 - 1.0
            $table->longText('linkedin_post')->nullable();
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
