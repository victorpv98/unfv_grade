<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grade_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_student_id')->constrained('course_students')->cascadeOnDelete();
            $table->smallInteger('practice1')->nullable();
            $table->smallInteger('practice2')->nullable();
            $table->smallInteger('practice3')->nullable();
            $table->smallInteger('practice4')->nullable();
            $table->smallInteger('midterm')->nullable();
            $table->smallInteger('final')->nullable();
            $table->smallInteger('substitute')->nullable(); // reemplaza parcial o final
            $table->smallInteger('makeup')->nullable(); // aplazado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_details');
    }
};
