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
        Schema::create('course_acta_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_acta_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->enum('type', ['generated', 'uploaded']);
            $table->string('disk');
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->string('checksum')->nullable();
            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['course_acta_id', 'version']);
            $table->index(['course_acta_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_acta_files');
    }
};
