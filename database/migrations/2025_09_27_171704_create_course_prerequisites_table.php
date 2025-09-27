<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('course_prerequisites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $t->foreignId('prerequisite_course_id')->constrained('courses')->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['course_id','prerequisite_course_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('course_prerequisites');
    }
};