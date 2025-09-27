<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('course_teachers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('period_id')->constrained()->cascadeOnDelete();
            $t->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['course_id','period_id','teacher_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('course_teachers');
    }
};