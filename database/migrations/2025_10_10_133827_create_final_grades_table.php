<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_student_id')->constrained('course_students')->cascadeOnDelete();
            $table->decimal('average', 5, 2);
            $table->enum('status', ['A', 'D', 'S', 'R']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_grades');
    }
};
