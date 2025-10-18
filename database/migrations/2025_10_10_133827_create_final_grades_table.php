<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_student_id')->constrained('course_students')->cascadeOnDelete();
            $table->decimal('average', 5, 2);
            $table->string('status', 10);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE final_grades ADD CONSTRAINT chk_final_status CHECK (status IN ('A','D','S','R'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('final_grades');
    }
};
