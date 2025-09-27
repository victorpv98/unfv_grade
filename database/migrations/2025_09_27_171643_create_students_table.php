<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('code', 10)->unique();      // código universitario
            $t->smallInteger('entry_year');
            $t->string('plan', 20)->nullable();    // malla curricular
            $t->foreignId('school_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('students');
    }
};