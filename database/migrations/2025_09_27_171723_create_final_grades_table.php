<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('final_grades', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('period_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $t->decimal('numeric_grade', 5, 2)->nullable(); // 0..20
            $t->enum('condition', ['aprobado','desaprobado','NP','retirado','aprobado_aplazado'])->nullable();
            $t->timestamp('computed_at')->nullable();
            $t->timestamps();

            $t->unique(['course_id','period_id','student_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('final_grades');
    }
};