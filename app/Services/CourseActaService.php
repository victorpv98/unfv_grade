<?php

namespace App\Services;

use App\Models\{
    Course,
    CourseActa,
    CourseActaFile,
    CourseStudent
};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class CourseActaService
{
    private string $defaultDisk;

    public function __construct()
    {
        $this->defaultDisk = config('filesystems.default', 'local');
    }

    /**
     * Generate the acta PDF for the given course and persist it in storage.
     *
     * @param  Course  $course
     * @param  CourseActa  $acta
     * @param  string|null  $disk
     * @return CourseActaFile
     */
    public function generateAndStorePdf(Course $course, CourseActa $acta, ?string $disk = null): CourseActaFile
    {
        $disk = $disk ?? $this->defaultDisk;

        $students = $this->getStudents($course, $acta->period_id);
        $teacherAssignment = $course->courseTeachers()
            ->with('teacher.user')
            ->when($acta->period_id, fn($query) => $query->where('period_id', $acta->period_id))
            ->latest()
            ->first();

        $signature = $this->resolveSignatureData($acta);

        $viewData = [
            'course' => $course->loadMissing('school'),
            'acta' => $acta,
            'teacherAssignment' => $teacherAssignment,
            'students' => $students,
            'signature' => $signature,
            'generatedAt' => now(),
            'stats' => [
                'total' => $students->count(),
                'approved' => $students->filter(fn($courseStudent) => optional($courseStudent->finalGrade)->status === 'A')->count(),
                'disapproved' => $students->filter(fn($courseStudent) => optional($courseStudent->finalGrade)->status === 'D')->count(),
                'substitute' => $students->filter(fn($courseStudent) => optional($courseStudent->finalGrade)->status === 'S')->count(),
                'makeup' => $students->filter(fn($courseStudent) => optional($courseStudent->finalGrade)->status === 'R')->count(),
                'average' => $this->calculateAverage($students),
            ],
        ];

        $pdf = Pdf::loadView('pdf.course-acta', $viewData)
            ->setPaper('a4', 'portrait');

        $fileName = $this->generateFilename($course);
        $relativePath = $this->buildStoragePath($course, $fileName);

        Storage::disk($disk)->put($relativePath, $pdf->output());

        $file = $acta->files()->create([
            'version' => $this->nextVersion($acta),
            'type' => CourseActaFile::TYPE_GENERATED,
            'disk' => $disk,
            'path' => $relativePath,
            'filename' => $fileName,
            'mime_type' => 'application/pdf',
            'file_size' => Storage::disk($disk)->size($relativePath),
            'checksum' => sha1(Storage::disk($disk)->get($relativePath)),
            'uploaded_by' => $acta->generated_by,
            'uploaded_at' => now(),
            'metadata' => [
                'student_count' => $students->count(),
                'period_id' => $acta->period_id,
                'signature' => $signature ? collect($signature)->except('data_url')->toArray() : null,
            ],
        ]);

        return $file;
    }

    /**
     * Persist an externally signed PDF and register it as a new version.
     */
    public function storeSignedPdf(CourseActa $acta, UploadedFile $file, int $uploadedBy, ?string $disk = null): CourseActaFile
    {
        $disk = $disk ?? $this->defaultDisk;
        $acta->loadMissing('course');

        $fileName = $this->buildSignedFilename($acta, $file);
        $relativePath = sprintf('course-actas/%s/signed/%s', $acta->course_id, $fileName);

        Storage::disk($disk)->putFileAs(
            dirname($relativePath),
            $file,
            basename($relativePath)
        );

        return $acta->files()->create([
            'version' => $this->nextVersion($acta),
            'type' => CourseActaFile::TYPE_UPLOADED,
            'disk' => $disk,
            'path' => $relativePath,
            'filename' => $fileName,
            'mime_type' => $file->getMimeType() ?: 'application/pdf',
            'file_size' => Storage::disk($disk)->size($relativePath),
            'checksum' => sha1(Storage::disk($disk)->get($relativePath)),
            'uploaded_by' => $uploadedBy,
            'uploaded_at' => now(),
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
            ],
        ]);
    }

    public function storeSignatureFromUpload(CourseActa $acta, UploadedFile $file, int $uploadedBy, ?string $disk = null): void
    {
        $disk = $disk ?? $this->defaultDisk;
        $acta->loadMissing('course');

        $directory = sprintf('course-actas/%s/signatures', $acta->course_id);
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $fileName = sprintf(
            'firma-%s-%s.%s',
            Str::slug($acta->course?->code ?? 'curso'),
            now()->format('Ymd_His'),
            $extension
        );

        $path = $file->storeAs($directory, $fileName, $disk);

        $acta->forceFill([
            'signature_disk' => $disk,
            'signature_path' => $path,
            'signature_mime_type' => $file->getMimeType() ?: 'image/png',
            'signature_original_name' => $file->getClientOriginalName(),
            'signature_type' => 'upload',
            'signature_uploaded_by' => $uploadedBy,
            'signature_uploaded_at' => now(),
        ])->save();
    }

    public function storeSignatureFromCanvas(CourseActa $acta, string $dataUrl, int $uploadedBy, ?string $disk = null): void
    {
        $disk = $disk ?? $this->defaultDisk;
        $acta->loadMissing('course');

        if (!preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
            throw new InvalidArgumentException('Formato de firma inválido.');
        }

        $extension = strtolower($matches[1]);
        $allowed = ['png', 'jpg', 'jpeg'];

        if (!in_array($extension, $allowed, true)) {
            throw new InvalidArgumentException('El formato de la firma no es soportado.');
        }

        $mime = $extension === 'jpg' ? 'image/jpeg' : "image/{$extension}";
        $data = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));

        if ($data === false) {
            throw new InvalidArgumentException('No se pudo procesar la firma dibujada.');
        }

        if (strlen($data) > 2 * 1024 * 1024) {
            throw new InvalidArgumentException('La firma dibujada supera el tamaño máximo permitido (2MB).');
        }

        $directory = sprintf('course-actas/%s/signatures', $acta->course_id);
        $fileName = sprintf(
            'firma-%s-%s.%s',
            Str::slug($acta->course?->code ?? 'curso'),
            now()->format('Ymd_His'),
            $extension
        );

        $path = "{$directory}/{$fileName}";
        Storage::disk($disk)->put($path, $data);

        $acta->forceFill([
            'signature_disk' => $disk,
            'signature_path' => $path,
            'signature_mime_type' => $mime,
            'signature_original_name' => $fileName,
            'signature_type' => 'drawn',
            'signature_uploaded_by' => $uploadedBy,
            'signature_uploaded_at' => now(),
        ])->save();
    }

    public function getStudents(Course $course, ?int $periodId = null): Collection
    {
        return CourseStudent::with([
            'student.user',
            'finalGrade',
            'gradeDetail',
        ])
            ->where('course_id', $course->id)
            ->when($periodId, fn($query) => $query->where('period_id', $periodId))
            ->get()
            ->sortBy(fn($courseStudent) => $courseStudent->student?->user?->name ?? '')
            ->values();
    }

    private function calculateAverage(Collection $students): float
    {
        $averages = $students
            ->map(fn($courseStudent) => $courseStudent->finalGrade?->average)
            ->filter(fn($average) => !is_null($average))
            ->all();

        if (empty($averages)) {
            return 0.0;
        }

        return round(array_sum($averages) / count($averages), 2);
    }

    public function resolveSignatureData(CourseActa $acta): ?array
    {
        if (!$acta->signature_path) {
            return null;
        }

        $disk = $acta->signature_disk ?: $this->defaultDisk;

        try {
            $raw = Storage::disk($disk)->get($acta->signature_path);
        } catch (FileNotFoundException) {
            return null;
        }

        $mime = $acta->signature_mime_type ?: 'image/png';

        return [
            'mime' => $mime,
            'original_name' => $acta->signature_original_name,
            'type' => $acta->signature_type,
            'data_url' => sprintf('data:%s;base64,%s', $mime, base64_encode($raw)),
        ];
    }

    private function generateFilename(Course $course): string
    {
        $timestamp = now()->format('Ymd_His');
        $slug = Str::slug($course->code . '-' . $course->name);

        return sprintf('acta-%s-%s.pdf', $slug, $timestamp);
    }

    private function buildStoragePath(Course $course, string $fileName): string
    {
        return sprintf('course-actas/%s/%s', $course->id, $fileName);
    }

    private function buildSignedFilename(CourseActa $acta, UploadedFile $file): string
    {
        $timestamp = now()->format('Ymd_His');
        $slug = Str::slug($acta->course?->code . '-' . $acta->course?->name);
        $extension = $file->getClientOriginalExtension() ?: 'pdf';

        return sprintf('acta-firmada-%s-%s.%s', $slug, $timestamp, $extension);
    }

    private function nextVersion(CourseActa $acta): int
    {
        $lastVersion = $acta->files()->max('version');

        return ($lastVersion ?? 0) + 1;
    }
}
