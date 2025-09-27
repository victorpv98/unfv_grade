<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('courses', function (Blueprint $t) {
            $t->id();
            $t->string('code', 20)->unique(); // INF-123
            $t->string('name', 160);
            $t->tinyInteger('credits')->default(0);
            $t->foreignId('school_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['obligatorio','electivo'])->default('obligatorio');
            $t->tinyInteger('level')->nullable(); // ciclo/semestre
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('courses');
    }
};