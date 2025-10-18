<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'admission_year') && ! Schema::hasColumn('students', 'enrollment_year')) {
                $table->renameColumn('admission_year', 'enrollment_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'enrollment_year') && ! Schema::hasColumn('students', 'admission_year')) {
                $table->renameColumn('enrollment_year', 'admission_year');
            }
        });
    }
};
