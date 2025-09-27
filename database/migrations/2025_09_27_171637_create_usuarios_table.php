<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuarios', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('email', 120)->unique();
            $t->string('password');
            $t->enum('role', ['student','teacher','admin'])->index();
            $t->foreignId('school_id')->nullable()->constrained()->nullOnDelete(); // admins -> null
            $t->enum('document_type', ['DNI','CE','PAS'])->default('DNI');
            $t->string('document_number', 20)->unique();
            $t->enum('status', ['active','suspended'])->default('active');
            $t->timestamp('email_verified_at')->nullable();
            $t->rememberToken();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('usuarios');
    }
};