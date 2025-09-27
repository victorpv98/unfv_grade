<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teachers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->enum('academic_category', ['principal','asociado','auxiliar','contratado'])->default('contratado');
            $t->foreignId('school_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('teachers');
    }
};