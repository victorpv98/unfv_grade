<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('periods', function (Blueprint $t) {
            $t->id();
            $t->string('code', 20)->unique(); // ej. 2025-1
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->enum('status', ['planned','open','closed','archived'])->default('planned');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('periods');
    }
};