<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10)->unique(); // Ejemplo: 2025-1
            $table->string('status', 15)->default('active');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE periods ADD CONSTRAINT chk_period_status CHECK (status IN ('active','closed'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
