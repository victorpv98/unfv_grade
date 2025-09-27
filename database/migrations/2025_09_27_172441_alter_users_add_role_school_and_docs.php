<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users','role')) {
                $t->enum('role', ['student','teacher','admin'])->default('student')->index();
            }
            if (!Schema::hasColumn('users','school_id')) {
                $t->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('users','document_type')) {
                $t->enum('document_type', ['DNI','CE','PAS'])->default('DNI');
            }
            if (!Schema::hasColumn('users','document_number')) {
                $t->string('document_number', 20)->unique()->nullable();
            }
            if (!Schema::hasColumn('users','status')) {
                $t->enum('status', ['active','suspended'])->default('active');
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users','status'))           $t->dropColumn('status');
            if (Schema::hasColumn('users','document_number'))  $t->dropUnique(['document_number']);
            if (Schema::hasColumn('users','document_type'))    $t->dropColumn('document_type');
            if (Schema::hasColumn('users','school_id'))        $t->dropConstrainedForeignId('school_id');
            if (Schema::hasColumn('users','role'))             $t->dropIndex(['role']); // puede requerir nombre si lo generó
            if (Schema::hasColumn('users','role'))             $t->dropColumn('role');
        });
    }
};