<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_code_unique');
            $table->string('code', 20)->change();
            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_code_unique');
            $table->string('code', 15)->change();
            $table->unique('code');
        });
    }
};