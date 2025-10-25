<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Course, School, CoursePrerequisite};

class InformaticaCoursesSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::updateOrCreate(
            ['code' => 'INFO'],
            ['name' => 'Ingeniería Informática']
        );

        $courses = [
            ['100549', 'Lenguaje y Comunicación', 3, []],
            ['100550', 'Filosofía y Ética', 3, []],
            ['100551', 'Metodología del Trabajo Universitario', 2, []],
            ['100552', 'Actividades Culturales y Deportivas', 1, []],
            ['101007', 'Matemática Básica', 4, []],
            ['100553', 'Fundamentos de Cálculo', 3, []],
            ['101008', 'Introducción a la Ingeniería Informática', 3, []],
            ['100375', 'Inglés I', 1, []],
            ['100555', 'Liderazgo y Desarrollo Personal', 3, []],
            ['100556', 'Medio Ambiente y Desarrollo Sostenible', 3, []],
            ['100557', 'Tecnologías de la Información', 2, []],
            ['100442', 'Sociología', 2, []],
            ['101009', 'Matemática Discreta para Informática', 3, ['101007','101008']],
            ['100558', 'Cálculo Integral', 4, ['100553']],
            ['100560', 'Física', 4, []],
            ['100382', 'Inglés II', 1, []],
            ['100003', 'Psicología Organizacional', 2, []],
            ['103263', 'Estadística', 3, []],
            ['100377', 'Metodología de la Investigación Científica', 3, []],
            ['100561', 'Geopolítica y Realidad Nacional', 3, []],
            ['101010', 'Lógica Digital', 3, ['101009','100560']],
            ['101011', 'Matemática Aplicada', 3, ['100558']],
            ['101012', 'Lenguaje de Programación I', 4, ['101009']],
            ['100387', 'Inglés III', 1, []],
            ['100581', 'Métodos Numéricos', 3, ['103263']],
            ['101013', 'Base de Datos I', 3, ['101012']],
            ['101014', 'Teoría de Comunicaciones', 3, ['101010']],
            ['101015', 'Electrónica Digital', 4, ['101010']],
            ['101016', 'Arquitectura y Organización del Computador', 3, ['101011']],
            ['101017', 'Lenguaje de Programación II', 3, ['101012']],
            ['101018', 'Inglés Aplicado a la Informática I', 3, ['100387']],
            ['101019', 'Contabilidad y Costos', 3, ['100581']],
            ['101020', 'Base de Datos II', 3, ['101013']],
            ['101021', 'Dispositivos Móviles I', 4, ['101014','101015']],
            ['100636', 'Proyecto Integrador I', 3, ['101013','101017']],
            ['101022', 'Lenguaje de Programación III', 3, ['101017']],
            ['101023', 'Inglés Aplicado a la Informática II', 3, ['101018']],
            ['101024', 'Investigación de Operaciones', 3, ['101019']],
            ['101025', 'Gestión y Análisis de Datos e Información', 4, ['101020']],
            ['101026', 'Dispositivos Móviles II', 3, ['101021']],
            ['100993', 'Redes y Conectividad', 3, ['101021']],
            ['101027', 'Teleinformática I', 3, ['101016']],
            ['101028', 'Inglés Aplicado a la Informática III', 3, ['101023']],
            ['101029', 'Ingeniería de Sistemas de Información', 4, ['101024']],
            ['100908', 'Formulación y Evaluación de Proyectos', 3, ['101025']],
            ['101030', 'Proyecto Integrador II', 4, ['101026']],
            ['101031', 'Planeamiento Estratégico de la Información', 3, ['101025']],
            ['101032', 'Ingeniería Económica', 2, ['101024']],
            ['101033', 'Teleinformática II', 3, ['101027']],
            ['101034', 'Finanzas para Empresas', 3, ['101024']],
            ['101035', 'Dinámica de Sistemas de Información', 3, ['101029']],
            ['101036', 'Tópicos Avanzados en Programación', 3, ['101030']],
            ['101037', 'Innovación y Tecnología', 3, ['101031']],
            ['101038', 'Sistemas Operativos', 3, ['101026']],
            ['101039', 'Proyectos Informáticos', 3, ['101031']],
            ['101040', 'Prospectiva Empresarial', 3, ['101034','101037']],
            ['101041', 'Simulación de Sistemas Informáticos y Empresariales', 3, ['101035']],
            ['101042', 'Control y Calidad de Software', 3, ['101036']],
            ['101043', 'Proyecto Integrador en Dispositivos Móviles', 3, ['101036']],
            ['101044', 'Sistemas de Información Gerencial', 3, ['101034']],
            ['100597', 'Taller de Tesis I', 2, ['101039']],
            ['100996', 'Prácticas Pre Profesionales I', 2, ['101039']],
            ['101045', 'Gerencia y Consultoría Informática', 3, ['101040']],
            ['101046', 'Ingeniería del Conocimiento', 3, ['101041']],
            ['101047', 'Seguridad y Auditoría Informática', 3, ['101042']],
            ['101048', 'Tecnologías Emergentes', 3, ['101043']],
            ['101049', 'Tecnología E-Business', 3, ['101043']],
            ['100603', 'Taller de Tesis II', 2, ['100597']],
            ['101003', 'Prácticas Pre Profesionales II', 2, ['100996']],
        ];

        $map = [];

        foreach ($courses as [$code, $name, $credits, $prereqs]) {
            $course = Course::updateOrCreate(
                ['code' => $code],
                ['school_id' => $school->id, 'name' => $name, 'credits' => $credits]
            );
            $map[$code] = $course->id;
        }

        foreach ($courses as [$code, , , $prereqs]) {
            $courseId = $map[$code];
            foreach ($prereqs as $reqCode) {
                if (isset($map[$reqCode])) {
                    CoursePrerequisite::firstOrCreate([
                        'course_id' => $courseId,
                        'prerequisite_id' => $map[$reqCode],
                    ]);
                }
            }
        }

        echo "Cursos de Ingeniería Informática (UNFV 2019) registrados exitosamente.\n";
    }
}
