<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Course,
    School,
    CourseStudent,
    Student,
    Teacher,
    CourseTeacher,
    FinalGrade,
    Period
};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $activePeriod = $this->getActivePeriod();

        $coursesQuery = Course::query()
            ->with([
                'school',
                'courseTeachers' => function ($query) use ($activePeriod) {
                    $query->with('teacher.user');

                    if ($activePeriod) {
                        $query->where('period_id', $activePeriod->id);
                    }
                },
            ])
            ->orderBy('name');

        $coursesQuery->withCount([
            'courseStudents as students_count' => function ($query) use ($activePeriod) {
                if ($activePeriod) {
                    $query->where('period_id', $activePeriod->id);
                }
            },
        ]);

        $search = trim((string) $request->input('search', ''));
        $schoolId = $request->input('school_id');
        $teacherId = $request->input('teacher_id');

        if ($search !== '') {
            $coursesQuery->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($schoolId !== null && $schoolId !== '') {
            $coursesQuery->where('school_id', $schoolId);
        }

        if ($teacherId !== null && $teacherId !== '') {
            $coursesQuery->whereHas('courseTeachers', function ($query) use ($teacherId, $activePeriod) {
                $query->where('teacher_id', $teacherId);

                if ($activePeriod) {
                    $query->where('period_id', $activePeriod->id);
                }
            });
        }

        $courses = $coursesQuery->paginate(20)->withQueryString();
        $schools = School::orderBy('name')->get();
        $teachers = $this->availableTeachers();

        $filters = [
            'search' => $search,
            'school_id' => $schoolId,
            'teacher_id' => $teacherId,
        ];

        $hasFilters = $search !== '' || ($schoolId !== null && $schoolId !== '') || ($teacherId !== null && $teacherId !== '');

        return view('admin.courses.index', compact('courses', 'activePeriod', 'schools', 'teachers', 'filters', 'hasFilters'));
    }

    public function create()
    {
        $schools = School::orderBy('name')->get();
        $teachers = $this->availableTeachers();
        $allCourses = Course::orderBy('code')->get(['id', 'code', 'name']);
        $selectedPrerequisites = [];

        return view('admin.courses.create', compact('schools', 'teachers', 'allCourses', 'selectedPrerequisites'));
    }

    public function store(Request $request)
    {
        $activePeriod = $this->getActivePeriod();

        if (!$activePeriod) {
            return back()
                ->withInput()
                ->with('error', 'No existe un periodo activo. Configure uno antes de registrar cursos.');
        }

        $validated = $this->validateCourse($request);

        DB::transaction(function () use ($validated, $request, $activePeriod) {
            $course = Course::create($this->courseData($validated));

            $this->syncPrerequisites($course, $request->input('prerequisites', []));
            $this->assignTeacher($course, (int) $request->input('teacher_id'), $activePeriod->id);
        });

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Curso creado correctamente.');
    }

    public function edit(Course $course)
    {
        $course->load('prerequisites');

        $schools = School::orderBy('name')->get();
        $teachers = $this->availableTeachers();
        $allCourses = Course::where('id', '!=', $course->id)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);
        $selectedPrerequisites = $course->prerequisites()
            ->pluck('courses.id')
            ->all();

        $activePeriod = $this->getActivePeriod();
        $currentTeacherId = $activePeriod
            ? $course->courseTeachers()
                ->where('period_id', $activePeriod->id)
                ->value('teacher_id')
            : null;

        return view('admin.courses.edit', compact(
            'course',
            'schools',
            'teachers',
            'allCourses',
            'currentTeacherId',
            'selectedPrerequisites'
        ));
    }

    public function update(Request $request, Course $course)
    {
        $activePeriod = $this->getActivePeriod();

        if (!$activePeriod) {
            return back()
                ->withInput()
                ->with('error', 'No existe un periodo activo. Configure uno antes de actualizar cursos.');
        }

        $validated = $this->validateCourse($request, $course);

        DB::transaction(function () use ($course, $validated, $request, $activePeriod) {
            $course->update($this->courseData($validated));

            $this->syncPrerequisites($course, $request->input('prerequisites', []));
            $this->assignTeacher($course, (int) $request->input('teacher_id'), $activePeriod->id);
        });

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return back()->with('success', 'Curso eliminado correctamente.');
    }

    public function students(Course $course)
    {
        $activePeriod = $this->getActivePeriod();
        $course->load(['school', 'prerequisites']);

        $enrollmentsQuery = $course->courseStudents()
            ->with(['student.user', 'finalGrade'])
            ->orderByDesc('created_at');

        if ($activePeriod) {
            $enrollmentsQuery->where('period_id', $activePeriod->id);
        }

        $enrollments = $enrollmentsQuery->get();

        $availableStudentsQuery = Student::with('user')
            ->orderBy('code');

        if ($activePeriod) {
            $alreadyEnrolled = $enrollments->pluck('student_id');
            if ($alreadyEnrolled->isNotEmpty()) {
                $availableStudentsQuery->whereNotIn('id', $alreadyEnrolled);
            }
        }

        $studentsOptions = $availableStudentsQuery->get();

        return view('admin.courses.students', [
            'course' => $course,
            'enrollments' => $enrollments,
            'studentsOptions' => $studentsOptions,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function checkPrerequisites(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $student = Student::with('user')->findOrFail($request->input('student_id'));
        $course->load('prerequisites');

        $check = $this->prerequisitesStatus($course, $student);

        if ($check['is_valid']) {
            return response()->json([
                'status' => 'ok',
                'message' => 'El estudiante cumple los requisitos para matricularse en este curso.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'El estudiante no cumple los requisitos para matricularse en este curso.',
            'missing' => $check['missing'],
        ], 422);
    }

    public function enrollStudent(Request $request, Course $course)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $activePeriod = $this->getActivePeriod();

        if (!$activePeriod) {
            return $this->enrollmentErrorResponse(
                $request,
                'No existe un periodo activo. Configure uno antes de matricular estudiantes.'
            );
        }

        $student = Student::with('user')->findOrFail($request->input('student_id'));

        $alreadyEnrolled = $course->courseStudents()
            ->where('student_id', $student->id)
            ->where('period_id', $activePeriod->id)
            ->exists();

        if ($alreadyEnrolled) {
            return $this->enrollmentErrorResponse(
                $request,
                'El estudiante ya se encuentra matriculado en este curso para el periodo activo.'
            );
        }

        $course->load('prerequisites');
        $check = $this->prerequisitesStatus($course, $student);

        if (!$check['is_valid']) {
            return $this->enrollmentErrorResponse(
                $request,
                'El estudiante no cumple los requisitos para matricularse en este curso.'
            );
        }

        CourseStudent::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'period_id' => $activePeriod->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Estudiante matriculado correctamente.',
            ]);
        }

        return redirect()
            ->route('admin.courses.students', $course)
            ->with('success', 'Estudiante matriculado correctamente.');
    }

    private function getActivePeriod(): ?Period
    {
        return Period::where('status', 'active')->first();
    }

    private function availableTeachers()
    {
        return Teacher::with('user')
            ->get()
            ->sortBy(function ($teacher) {
                $name = optional($teacher->user)->name ?? '';

                return mb_strtolower($name);
            })
            ->values();
    }

    private function courseData(array $validated): array
    {
        return collect($validated)->only(['code', 'name', 'credits', 'school_id'])->toArray();
    }

    private function syncPrerequisites(Course $course, array $prerequisites): void
    {
        $ids = collect($prerequisites)
            ->filter()
            ->unique()
            ->reject(fn($id) => (int) $id === $course->id)
            ->values()
            ->all();

        $course->prerequisites()->sync($ids);
    }

    private function assignTeacher(Course $course, int $teacherId, int $periodId): void
    {
        CourseTeacher::where('course_id', $course->id)
            ->where('period_id', $periodId)
            ->delete();

        CourseTeacher::create([
            'course_id' => $course->id,
            'teacher_id' => $teacherId,
            'period_id' => $periodId,
        ]);
    }

    private function prerequisitesStatus(Course $course, Student $student): array
    {
        $missing = [];

        foreach ($course->prerequisites as $prerequisite) {
            $finalGrade = FinalGrade::whereHas('courseStudent', function ($query) use ($prerequisite, $student) {
                $query->where('course_id', $prerequisite->id)
                    ->where('student_id', $student->id);
            })
                ->orderByDesc('created_at')
                ->first();

            $status = $finalGrade?->status;
            $normalizedStatus = strtolower(trim((string) $status));
            $approvedStatuses = ['a', 'aprobado', 'approved'];

            if (!$status || !in_array($normalizedStatus, $approvedStatuses, true)) {
                $missing[] = "{$prerequisite->code} · {$prerequisite->name}";
            }
        }

        return [
            'is_valid' => empty($missing),
            'missing' => $missing,
        ];
    }

    private function validateCourse(Request $request, ?Course $course = null): array
    {
        $codeRule = Rule::unique('courses', 'code');

        if ($course) {
            $codeRule->ignore($course->id);
        }

        return $request->validate([
            'code' => [
                'required',
                'max:10',
                $codeRule,
            ],
            'name' => ['required', 'max:150'],
            'credits' => ['required', 'integer', 'min:1'],
            'school_id' => ['required', 'exists:schools,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['integer', 'exists:courses,id'],
        ]);
    }

    private function enrollmentErrorResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], 422);
        }

        return back()
            ->withInput()
            ->with('error', $message);
    }
}
