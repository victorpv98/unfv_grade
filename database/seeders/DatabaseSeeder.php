<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    School, User, Student, Teacher, Period, Course,
    CourseTeacher, CourseStudent, GradeDetail
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // ---------- Escuelas (FIEI) ----------
            $schools = [
                ['code' => 'ELEC', 'name' => 'Ingeniería Electrónica'],
                ['code' => 'INFO', 'name' => 'Ingeniería Informática'],
                ['code' => 'MECA', 'name' => 'Ingeniería Mecatrónica'],
                ['code' => 'TELE', 'name' => 'Ingeniería de Telecomunicaciones'],
            ];

            $schoolsByCode = [];
            foreach ($schools as $s) {
                $schoolsByCode[$s['code']] = School::updateOrCreate(
                    ['code' => $s['code']],
                    ['name' => $s['name']]
                );
            }
            $schoolIds = array_map(fn ($m) => $m->id, $schoolsByCode);

            // ---------- Usuario Admin ----------
            User::updateOrCreate(
                ['email' => 'admin@unfv.edu.pe'],
                [
                    'name'     => 'Administrador FIEI',
                    'password' => Hash::make('1234'),
                    'role'     => 'admin',
                    'status'   => 'active',
                ]
            );

            // ---------- Docentes ----------
            $u1 = User::updateOrCreate(
                ['email' => 'juan@unfv.edu.pe'],
                [
                    'name'      => 'Dr. Juan Pérez',
                    'password'  => Hash::make('1234'),
                    'role'      => 'teacher',
                    'school_id' => $schoolsByCode['ELEC']->id,
                ]
            );
            $u2 = User::updateOrCreate(
                ['email' => 'ana@unfv.edu.pe'],
                [
                    'name'      => 'Ing. Ana Torres',
                    'password'  => Hash::make('1234'),
                    'role'      => 'teacher',
                    'school_id' => $schoolsByCode['INFO']->id,
                ]
            );

            $t1 = Teacher::firstOrCreate(['user_id' => $u1->id]);
            $t2 = Teacher::firstOrCreate(['user_id' => $u2->id]);

            // ---------- Estudiantes demo ----------
            $countSchools = count($schoolIds);
            for ($i = 1; $i <= 3; $i++) {
                $studentUser = User::updateOrCreate(
                    ['email' => "estudiante{$i}@unfv.edu.pe"],
                    [
                        'name'      => "Estudiante {$i}",
                        'password'  => Hash::make('1234'),
                        'role'      => 'student',
                        'school_id' => $schoolIds[($i - 1) % $countSchools],
                    ]
                );

                Student::updateOrCreate(
                    ['code' => '2025' . $i],
                    [
                        'user_id'         => $studentUser->id,
                        'enrollment_year' => 2025,
                    ]
                );
            }

            // ---------- Periodo activo ----------
            $period = Period::updateOrCreate(
                ['name' => '2025-1'],
                ['status' => 'active']
            );

            // ---------- Cursos base ----------
            $c1 = Course::updateOrCreate(
                ['code' => 'EL101'],
                [
                    'school_id' => $schoolsByCode['ELEC']->id,
                    'name'      => 'Calculo I',
                    'credits'   => 4,
                ]
            );
            $c2 = Course::updateOrCreate(
                ['code' => 'IN102'],
                [
                    'school_id' => $schoolsByCode['INFO']->id,
                    'name'      => 'LP I',
                    'credits'   => 4,
                ]
            );

            // ---------- Asignaciones ----------
            CourseTeacher::updateOrCreate(
                [
                    'course_id' => $c1->id,
                    'teacher_id'=> $t1->id,
                    'period_id' => $period->id,
                ],
                []
            );
            CourseTeacher::updateOrCreate(
                [
                    'course_id' => $c2->id,
                    'teacher_id'=> $t2->id,
                    'period_id' => $period->id,
                ],
                []
            );

            // ---------- Matrículas + detalles de nota ----------
            foreach (Student::pluck('id') as $studentId) {
                $courseStudent = CourseStudent::updateOrCreate(
                    [
                        'course_id'  => $c1->id,
                        'student_id' => $studentId,
                        'period_id'  => $period->id,
                    ],
                    []
                );

                GradeDetail::firstOrCreate([
                    'course_student_id' => $courseStudent->id,
                ]);
            }
        });

        $this->call([
            InformaticaCoursesSeeder::class,
        ]);
    }
}