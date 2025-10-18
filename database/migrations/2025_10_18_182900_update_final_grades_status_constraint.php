<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement("ALTER TABLE final_grades DROP CONSTRAINT IF EXISTS chk_final_status");

            DB::statement("
                UPDATE final_grades
                SET status = CASE
                    WHEN status IN ('approved', 'aprobado', 'Approved', 'Aprobado') THEN 'A'
                    WHEN status IN ('failed', 'desaprobado', 'Failed', 'Desaprobado') THEN 'D'
                    WHEN status IN ('sustitutorio', 'substitute', 'Sustitutorio', 'Substitute') THEN 'S'
                    WHEN status IN ('aplazado', 'makeup', 'Aplazado', 'Makeup') THEN 'R'
                    ELSE status
                END
            ");

            DB::statement("ALTER TABLE final_grades ADD CONSTRAINT chk_final_status CHECK (status IN ('A','D','S','R'))");
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement("ALTER TABLE final_grades DROP CONSTRAINT IF EXISTS chk_final_status");

            DB::statement("
                UPDATE final_grades
                SET status = CASE
                    WHEN status = 'A' THEN 'approved'
                    WHEN status = 'D' THEN 'failed'
                    WHEN status = 'S' THEN 'sustitutorio'
                    WHEN status = 'R' THEN 'aplazado'
                    ELSE status
                END
            ");

            DB::statement("ALTER TABLE final_grades ADD CONSTRAINT chk_final_status CHECK (status IN ('approved','failed','sustitutorio','aplazado'))");
        });
    }
};
