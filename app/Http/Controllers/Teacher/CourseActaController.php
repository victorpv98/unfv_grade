<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{
    Course,
    CourseActa,
    CourseActaFile,
    CourseTeacher,
    Period,
    User
};
use App\Services\CourseActaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseActaController extends Controller
{
    public function __construct(private CourseActaService $actaService)
    {
    }

    public function show(Request $request, Course $course): View
    {
        $teacher = Auth::user()->teacher;
        $this->ensureTeacherHasAccess($course, $teacher?->id);

        $period = $this->getActivePeriod();
        $acta = $this->resolveActa($course, $period?->id);

        $students = $this->actaService->getStudents($course, $period?->id);

        return view('teachers.course-acta', [
            'course' => $course->load('school'),
            'acta' => $acta->load(['course.school', 'period']),
            'students' => $students,
            'latestGenerated' => $acta->latestGeneratedFile,
            'latestUploaded' => $acta->latestUploadedFile,
            'activePeriod' => $period,
            'teacherAssignment' => $this->getTeacherAssignment($course, $teacher?->id, $period?->id),
            'stats' => $this->buildStats($students),
        ]);
    }

    public function generate(Request $request, Course $course): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        $this->ensureTeacherHasAccess($course, $teacher?->id);

        $data = $request->validate([
            'confirm_generation' => ['accepted'],
        ], [
            'confirm_generation.accepted' => 'Debe confirmar que la información es correcta antes de generar el acta.',
        ]);

        $period = $this->getActivePeriod();
        $acta = $this->resolveActa($course, $period?->id);

        $user = $request->user();

        $acta->forceFill([
            'generated_by' => $user->id,
            'generated_at' => now(),
        ])->save();

        $acta->markStatus(CourseActa::STATUS_GENERATED, $user->id);
        $acta->load('course');

        $file = $this->actaService->generateAndStorePdf($course, $acta);

        return redirect()
            ->route('teacher.courses.acta.show', $course)
            ->with('success', 'Acta generada correctamente. Puede descargarla o compartirla desde esta página.');
    }

    public function uploadSigned(Request $request, Course $course): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        $this->ensureTeacherHasAccess($course, $teacher?->id);

        $data = $request->validate([
            'signed_pdf' => ['required', 'file', 'mimetypes:application/pdf', 'max:8192'],
        ]);

        $period = $this->getActivePeriod();
        $acta = $this->resolveActa($course, $period?->id);
        $user = $request->user();

        $file = $this->actaService->storeSignedPdf($acta, $data['signed_pdf'], $user->id);
        $acta->markStatus(CourseActa::STATUS_SIGNED, $user->id);

        return redirect()
            ->route('teacher.courses.acta.show', $course)
            ->with('success', 'El PDF firmado se guardó correctamente y fue notificado al administrador.');
    }

    public function download(Course $course, CourseActaFile $file)
    {
        $teacher = Auth::user()->teacher;
        $this->ensureTeacherHasAccess($course, $teacher?->id);

        if ($file->acta->course_id !== $course->id) {
            abort(404);
        }

        if (!Storage::disk($file->disk)->exists($file->path)) {
            return back()->with('error', 'El archivo solicitado no se encuentra disponible.');
        }

        return Storage::disk($file->disk)->download($file->path, $file->filename);
    }

    private function ensureTeacherHasAccess(Course $course, ?int $teacherId): void
    {
        if (!$teacherId) {
            abort(403);
        }

        $period = $this->getActivePeriod();

        $exists = CourseTeacher::query()
            ->where('course_id', $course->id)
            ->when($period, fn($query) => $query->where('period_id', $period->id))
            ->where('teacher_id', $teacherId)
            ->exists();

        if (!$exists) {
            abort(403);
        }
    }

    private function resolveActa(Course $course, ?int $periodId): CourseActa
    {
        return CourseActa::firstOrCreate(
            [
                'course_id' => $course->id,
                'period_id' => $periodId,
            ],
            [
                'status' => CourseActa::STATUS_PENDING,
            ]
        );
    }

    private function getTeacherAssignment(Course $course, ?int $teacherId, ?int $periodId)
    {
        return CourseTeacher::with('teacher.user')
            ->where('course_id', $course->id)
            ->when($teacherId, fn($query) => $query->where('teacher_id', $teacherId))
            ->when($periodId, fn($query) => $query->where('period_id', $periodId))
            ->latest()
            ->first();
    }

    private function getActivePeriod(): ?Period
    {
        return Period::where('status', 'active')->first();
    }

    private function buildStats($students): array
    {
        $approved = $students->filter(fn($courseStudent) => optional($courseStudent->finalGrade)->status === 'A')->count();
        $disapproved = $students->filter(fn($courseStudent) => optional($courseStudent->finalGrade)->status === 'D')->count();
        $substitute = $students->filter(fn($courseStudent) => optional($courseStudent->finalGrade)->status === 'S')->count();
        $makeup = $students->filter(fn($courseStudent) => optional($courseStudent->finalGrade)->status === 'R')->count();

        $averages = $students
            ->map(fn($courseStudent) => $courseStudent->finalGrade?->average)
            ->filter(fn($average) => !is_null($average))
            ->all();

        $average = empty($averages)
            ? 0.0
            : round(array_sum($averages) / count($averages), 2);

        return [
            'total' => $students->count(),
            'approved' => $approved,
            'disapproved' => $disapproved,
            'substitute' => $substitute,
            'makeup' => $makeup,
            'average' => $average,
        ];
    }
}
