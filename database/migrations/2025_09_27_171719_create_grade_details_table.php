<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grade_details', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('period_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $t->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $t->enum('kind', ['PRACTICE','PARTIAL','FINAL','SUBSTITUTE','APLAZADO']);
            $t->tinyInteger('slot')->nullable(); 
            $t->decimal('score', 5, 2)->nullable(); 
            $t->enum('applied_to', ['PARTIAL','FINAL'])->nullable(); 
            $t->timestamps();

            $t->index(['course_id','period_id','student_id','kind']);

            $t->unique(['course_id','period_id','student_id','kind','slot']);

            $t->unique(['course_id','period_id','student_id','kind','applied_to']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('grade_details');
    }
};