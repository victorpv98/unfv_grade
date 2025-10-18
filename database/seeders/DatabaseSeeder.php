<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{School, User, Student, Teacher, Period, Course, CourseTeacher, CourseStudent, GradeDetail};

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Escuelas FIEI
        $schools = [
            ['code' => 'ELEC', 'name' => 'Ingeniería Electrónica'],
            ['code' => 'INFO', 'name' => 'Ingeniería Informática'],
            ['code' => 'MECA', 'name' => 'Ingeniería Mecatrónica'],
            ['code' => 'TELE', 'name' => 'Ingeniería de Telecomunicaciones'],
        ];
        foreach ($schools as $s) School::create($s);

        // Admin
        User::create([
            'name' => 'Administrador FIEI',
            'email' => 'admin@unfv.edu.pe',
            'password' => Hash::make('1234'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        // Docentes
        $u1 = User::create([
            'name' => 'Dr. Juan Pérez',
            'email' => 'juan@unfv.edu.pe',
            'password' => Hash::make('1234'),
            'role' => 'teacher',
            'school_id' => 1
        ]);
        $u2 = User::create([
            'name' => 'Ing. Ana Torres',
            'email' => 'ana@unfv.edu.pe',
            'password' => Hash::make('1234'),
            'role' => 'teacher',
            'school_id' => 2
        ]);
        $t1 = Teacher::create(['user_id' => $u1->id]);
        $t2 = Teacher::create(['user_id' => $u2->id]);

        // Estudiantes
        for ($i = 1; $i <= 3; $i++) {
            $u = User::create([
                'name' => "Estudiante $i",
                'email' => "estudiante$i@unfv.edu.pe",
                'password' => Hash::make('1234'),
                'role' => 'student',
                'school_id' => rand(1, 4)
            ]);
            Student::create([
                'user_id' => $u->id,
                'code' => '2025' . $i,
                'enrollment_year' => 2025
            ]);
        }

        // Periodo activo
        $period = Period::create(['name' => '2025-1', 'status' => 'active']);

        // Cursos
        $c1 = Course::create(['school_id' => 1, 'code' => 'EL101', 'name' => 'Calculo I', 'credits' => 4]);
        $c2 = Course::create(['school_id' => 2, 'code' => 'IN102', 'name' => 'LP I', 'credits' => 4]);

        // Asignaciones y matrículas
        CourseTeacher::create(['course_id' => $c1->id, 'teacher_id' => $t1->id, 'period_id' => $period->id]);
        CourseTeacher::create(['course_id' => $c2->id, 'teacher_id' => $t2->id, 'period_id' => $period->id]);

        foreach (Student::all() as $s) {
            $cs = CourseStudent::create([
                'course_id' => $c1->id,
                'student_id' => $s->id,
                'period_id' => $period->id
            ]);
            GradeDetail::create(['course_student_id' => $cs->id]);
        }
    }
}
