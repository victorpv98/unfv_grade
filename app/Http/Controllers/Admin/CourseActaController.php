<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    CourseActa,
    CourseActaFile,
    Period
};
use App\Services\CourseActaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseActaController extends Controller
{
    public function __construct(private CourseActaService $actaService)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = trim((string) $request->input('search'));
        $periodId = $request->input('period_id');

        $actasQuery = CourseActa::with([
            'course.school',
            'period',
            'latestGeneratedFile',
            'latestUploadedFile',
        ])->withCount('files')
            ->orderByDesc('updated_at');

        if ($status) {
            $actasQuery->where('status', $status);
        }

        if ($periodId) {
            $actasQuery->where('period_id', $periodId);
        }

        if ($search !== '') {
            $actasQuery->whereHas('course', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $actas = $actasQuery->paginate(20)->withQueryString();
        $periods = Period::orderByDesc('created_at')->get(['id', 'name']);

        $filters = [
            'status' => $status,
            'period_id' => $periodId,
            'search' => $search,
        ];

        return view('admin.course-actas.index', compact('actas', 'filters', 'periods'));
    }

    public function show(CourseActa $acta): View
    {
        $acta->load([
            'course.school',
            'period',
            'latestGeneratedFile',
            'latestUploadedFile',
            'generatedBy',
            'signatureUploadedBy',
            'lastStatusChangedBy',
        ]);

        $students = $this->actaService->getStudents($acta->course, $acta->period_id);

        return view('admin.course-actas.show', [
            'acta' => $acta,
            'students' => $students,
            'stats' => $this->buildStats($students),
            'signaturePreview' => $this->actaService->resolveSignatureData($acta),
        ]);
    }

    public function download(CourseActaFile $file)
    {
        $this->authorizeFile($file);

        if (!Storage::disk($file->disk)->exists($file->path)) {
            return back()->with('error', 'El archivo solicitado no se encuentra disponible.');
        }

        return Storage::disk($file->disk)->download($file->path, $file->filename);
    }

    public function preview(CourseActaFile $file)
    {
        $this->authorizeFile($file);

        if (!Storage::disk($file->disk)->exists($file->path)) {
            return back()->with('error', 'El archivo solicitado no se encuentra disponible.');
        }

        return response()->file(
            Storage::disk($file->disk)->path($file->path),
            [
                'Content-Type' => $file->mime_type,
                'Content-Disposition' => 'inline; filename="' . $file->filename . '"',
            ]
        );
    }

    private function authorizeFile(CourseActaFile $file): void
    {
        // Placeholder for future authorization policies if needed.
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
