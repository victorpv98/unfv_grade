<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 120)->unique();
            $table->string('password');
            $table->string('role', 20);
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->string('document_type', 10)->nullable();
            $table->string('document_number', 20)->unique()->nullable();
            $table->string('status', 15)->default('active');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_role CHECK (role IN ('admin','teacher','student'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_status CHECK (status IN ('active','suspended'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_document_type CHECK (document_type IN ('DNI','CE','PAS') OR document_type IS NULL)");
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};