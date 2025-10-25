<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Course, CourseStudent, GradeDetail, FinalGrade};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SplFileObject;

class CourseGradeController extends Controller
{
    public function index()
    {
        $teacherId = auth()->user()->teacher->id;
        $courses = Course::whereHas('courseTeachers', fn($q) => $q->where('teacher_id', $teacherId))->get();
        return view('teachers.my-courses', compact('courses'));
    }

    public function edit(Course $course)
    {
        $students = CourseStudent::with(['student.user', 'gradeDetail'])
            ->where('course_id', $course->id)
            ->get();

        return view('teachers.course-grades', compact('course', 'students'));
    }

    public function update(Request $request, Course $course)
    {
        DB::transaction(function () use ($request, $course) {
            foreach ($request->input('grades', []) as $studentId => $inputGrades) {
                $courseStudent = CourseStudent::where('student_id', $studentId)
                    ->where('course_id', $course->id)
                    ->first();

                if (!$courseStudent) {
                    continue;
                }

                $this->storeGradesForCourseStudent($courseStudent, $inputGrades);
            }
        });

        return back()->with('success', 'Notas actualizadas correctamente.');
    }

    public function downloadTemplate(Course $course)
    {
        $students = CourseStudent::with(['student.user', 'gradeDetail'])
            ->where('course_id', $course->id)
            ->get();

        $fileName = sprintf(
            'plantilla-notas-%s.csv',
            Str::slug($course->code . '-' . $course->name)
        );

        $gradeLabels = $this->gradeFieldLabels();
        $gradeKeys = array_keys($gradeLabels);

        return response()->streamDownload(function () use ($students, $gradeLabels, $gradeKeys) {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // Add UTF-8 BOM so Excel displays characters correctly.
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headerRow = array_merge(
                ['codigo_estudiante', 'nombre_estudiante'],
                array_map(fn($field) => sprintf('%s (%s)', $gradeLabels[$field], $field), $gradeKeys)
            );

            fputcsv($handle, $headerRow);

            foreach ($students as $courseStudent) {
                $detail = $courseStudent->gradeDetail;
                $student = $courseStudent->student;

                $row = [
                    $student?->code ?? '',
                    $student?->user?->name ?? '',
                ];

                foreach ($gradeKeys as $field) {
                    $row[] = $detail?->$field;
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function import(Request $request, Course $course)
    {
        $request->validate([
            'grades_file' => [
                'required',
                'file',
                'max:4096',
                'mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel',
            ],
        ]);

        $uploaded = $request->file('grades_file');
        if (!$uploaded) {
            return back()->with('error', 'No se pudo leer el archivo enviado.');
        }

        $path = $uploaded->getRealPath();
        if ($path === false) {
            return back()->with('error', 'El archivo seleccionado es inválido.');
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->with('error', 'No se pudo abrir el archivo para su lectura.');
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if ($firstLine === false) {
            return back()->with('error', 'El archivo está vacío.');
        }

        $delimiter = $this->detectDelimiter($firstLine);
        $headers = array_map(
            fn($value) => Str::slug($this->cleanCellValue($value), '_'),
            str_getcsv($firstLine, $delimiter)
        );

        $codeIndex = $this->findHeaderIndex($headers, ['codigo_estudiante', 'codigo', 'student_code']);
        if ($codeIndex === null) {
            return back()->with('error', 'La columna de código de estudiante es obligatoria (ej. "codigo_estudiante").');
        }

        $fieldIndexes = $this->resolveGradeIndexes($headers);
        if (!collect($fieldIndexes)->filter(fn($index) => $index !== null)->count()) {
            return back()->with('error', 'No se encontró ninguna columna de notas válida en el archivo.');
        }

        $courseStudents = CourseStudent::with(['student', 'gradeDetail'])
            ->where('course_id', $course->id)
            ->get()
            ->keyBy(fn($courseStudent) => $courseStudent->student?->code);

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl($delimiter);

        $updated = 0;
        $missingCodes = [];
        $skippedRows = [];
        $pendingUpdates = [];

        foreach ($file as $index => $row) {
            if ($index === 0) {
                continue; // Skip header row.
            }

            if (!is_array($row) || (count($row) === 1 && $row[0] === null)) {
                continue;
            }

            $row = array_map([$this, 'cleanCellValue'], $row);

            if ($this->isRowEmpty($row)) {
                continue;
            }

            $studentCode = $row[$codeIndex] ?? '';
            $studentCode = trim((string) $studentCode);

            if ($studentCode === '') {
                $skippedRows[] = $index + 1;
                continue;
            }

            $courseStudent = $courseStudents->get($studentCode);
            if (!$courseStudent) {
                $missingCodes[] = $studentCode;
                continue;
            }

            $gradesInput = [];
            foreach ($fieldIndexes as $field => $position) {
                if ($position === null) {
                    continue;
                }

                $gradesInput[$field] = $row[$position] ?? null;
            }

            $pendingUpdates[] = [$courseStudent, $gradesInput];
            $updated++;
        }

        if (!empty($pendingUpdates)) {
            DB::transaction(function () use ($pendingUpdates) {
                foreach ($pendingUpdates as [$courseStudent, $gradesInput]) {
                    $this->storeGradesForCourseStudent($courseStudent, $gradesInput);
                }
            });
        }

        $response = back()->with('success', "Notas importadas para {$updated} estudiante(s).");

        if ($missingCodes) {
            $missingCodes = collect($missingCodes)->unique()->values()->take(10)->implode(', ');
            $response->with('warning', "No se encontraron estudiantes con los códigos: {$missingCodes}.");
        }

        if ($skippedRows) {
            $rows = collect($skippedRows)->take(5)->implode(', ');
            $response->with('info', "Se omitieron filas sin código de estudiante (líneas: {$rows}).");
        }

        return $response;
    }

    public function summary(Course $course)
    {
        $grades = FinalGrade::with('courseStudent.student.user')
            ->whereHas('courseStudent', fn($q) => $q->where('course_id', $course->id))
            ->get();

        return view('teachers.final-summary', compact('course', 'grades'));
    }

    private function storeGradesForCourseStudent(CourseStudent $courseStudent, array $inputGrades): void
    {
        $normalized = [];

        foreach ($this->gradeFieldKeys() as $field) {
            $value = $inputGrades[$field] ?? null;

            if ($value === '' || $value === null) {
                $normalized[$field] = null;
                continue;
            }

            if (!is_numeric($value)) {
                $normalized[$field] = null;
                continue;
            }

            $normalized[$field] = (int) round((float) $value);
        }

        $detail = GradeDetail::updateOrCreate(
            ['course_student_id' => $courseStudent->id],
            $normalized
        );

        [$average, $status] = $this->calculateFinalValues($detail);

        FinalGrade::updateOrCreate(
            ['course_student_id' => $courseStudent->id],
            [
                'average' => $average,
                'status' => $status,
            ]
        );
    }

    private function calculateFinalValues(GradeDetail $detail): array
    {
        $baseScores = array_filter([
            $detail->practice1,
            $detail->practice2,
            $detail->practice3,
            $detail->practice4,
            $detail->midterm,
            $detail->final,
        ], static fn($score) => $score !== null);

        $average = !empty($baseScores)
            ? round(array_sum($baseScores) / count($baseScores), 2)
            : 0.0;

        if (!empty($baseScores) && $detail->substitute !== null && $detail->substitute > 0) {
            $lowest = min($baseScores);
            $replaceIndex = array_search($lowest, $baseScores, true);
            if ($replaceIndex !== false) {
                $baseScores[$replaceIndex] = $detail->substitute;
                $average = round(array_sum($baseScores) / count($baseScores), 2);
            }
        }

        if ($detail->makeup !== null && $detail->makeup > 0) {
            $average = round(($average + $detail->makeup) / 2, 2);
        }

        $status = match (true) {
            $detail->makeup !== null && $detail->makeup > 0 => $average >= 10.5 ? 'A' : 'R',
            $detail->substitute !== null && $detail->substitute > 0 => $average >= 10.5 ? 'A' : 'S',
            default => $average >= 10.5 ? 'A' : 'D',
        };

        return [$average, $status];
    }

    private function gradeFieldKeys(): array
    {
        return [
            'practice1',
            'practice2',
            'practice3',
            'practice4',
            'midterm',
            'final',
            'substitute',
            'makeup',
        ];
    }

    private function gradeFieldLabels(): array
    {
        return [
            'practice1' => 'P1',
            'practice2' => 'P2',
            'practice3' => 'P3',
            'practice4' => 'P4',
            'midterm' => 'Parcial',
            'final' => 'Final',
            'substitute' => 'Sustitutorio',
            'makeup' => 'Aplazado',
        ];
    }

    private function detectDelimiter(string $line): string
    {
        $line = $this->cleanCellValue($line);
        $delimiters = [',', ';', "\t"];
        $bestDelimiter = ',';
        $bestCount = 0;

        foreach ($delimiters as $delimiter) {
            $count = count(str_getcsv($line, $delimiter));
            if ($count > $bestCount) {
                $bestCount = $count;
                $bestDelimiter = $delimiter;
            }
        }

        return $bestDelimiter;
    }

    private function resolveGradeIndexes(array $headers): array
    {
        $options = [
            'practice1' => ['practice1', 'p1'],
            'practice2' => ['practice2', 'p2'],
            'practice3' => ['practice3', 'p3'],
            'practice4' => ['practice4', 'p4'],
            'midterm' => ['midterm', 'parcial'],
            'final' => ['final'],
            'substitute' => ['substitute', 'sustitutorio'],
            'makeup' => ['makeup', 'aplazado'],
        ];

        $indexes = [];

        foreach ($this->gradeFieldKeys() as $field) {
            $indexes[$field] = null;
            foreach ($options[$field] as $candidate) {
                $normalized = Str::slug($candidate, '_');
                $position = array_search($normalized, $headers, true);
                if ($position !== false) {
                    $indexes[$field] = $position;
                    break;
                }
            }
        }

        return $indexes;
    }

    private function findHeaderIndex(array $headers, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $normalized = Str::slug($candidate, '_');
            $index = array_search($normalized, $headers, true);
            if ($index !== false) {
                return $index;
            }
        }

        return null;
    }

    private function cleanCellValue($value): string
    {
        if ($value === null) {
            return '';
        }

        $value = (string) $value;
        // Remove UTF-8 BOM if present.
        return trim(preg_replace('/^\xEF\xBB\xBF/', '', $value));
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
