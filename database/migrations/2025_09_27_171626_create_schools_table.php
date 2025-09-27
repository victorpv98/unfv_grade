<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('schools', function (Blueprint $t) {
            $t->id();
            $t->string('code', 10)->unique();   // ELEC, INFO, MECA, TELE
            $t->string('name', 160);
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('schools');
    }
};