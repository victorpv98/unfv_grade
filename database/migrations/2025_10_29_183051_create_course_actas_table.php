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
        Schema::create('course_actas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('period_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->enum('status', ['pending', 'generated', 'signed'])
                ->default('pending');
            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->string('signature_disk')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('signature_mime_type')->nullable();
            $table->string('signature_original_name')->nullable();
            $table->string('signature_type')->nullable();
            $table->foreignId('signature_uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('signature_uploaded_at')->nullable();
            $table->foreignId('last_status_changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('last_status_changed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_actas');
    }
};
