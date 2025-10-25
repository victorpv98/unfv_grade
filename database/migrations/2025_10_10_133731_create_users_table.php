<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 120)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'teacher', 'student']);
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->enum('document_type', ['DNI', 'CE', 'PAS'])->nullable();
            $table->string('document_number', 20)->unique()->nullable();
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
