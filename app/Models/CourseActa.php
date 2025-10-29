<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class CourseActa extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_GENERATED = 'generated';
    public const STATUS_SIGNED = 'signed';

    protected $fillable = [
        'course_id',
        'period_id',
        'status',
        'generated_by',
        'generated_at',
        'signature_disk',
        'signature_path',
        'signature_mime_type',
        'signature_original_name',
        'signature_type',
        'signature_uploaded_by',
        'signature_uploaded_at',
        'last_status_changed_by',
        'last_status_changed_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'signature_uploaded_at' => 'datetime',
        'last_status_changed_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function signatureUploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signature_uploaded_by');
    }

    public function lastStatusChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_status_changed_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(CourseActaFile::class)->orderByDesc('version');
    }

    public function latestFile(): HasOne
    {
        return $this->hasOne(CourseActaFile::class)
            ->latestOfMany('version');
    }

    public function latestGeneratedFile(): HasOne
    {
        return $this->hasOne(CourseActaFile::class)
            ->where('type', CourseActaFile::TYPE_GENERATED)
            ->latestOfMany('version');
    }

    public function latestUploadedFile(): HasOne
    {
        return $this->hasOne(CourseActaFile::class)
            ->where('type', CourseActaFile::TYPE_UPLOADED)
            ->latestOfMany('version');
    }

    public function markStatus(string $status, ?int $byUserId = null): void
    {
        $this->forceFill([
            'status' => $status,
            'last_status_changed_by' => $byUserId,
            'last_status_changed_at' => now(),
        ])->save();
    }

    public function isGenerated(): bool
    {
        return $this->status === self::STATUS_GENERATED;
    }

    public function isSigned(): bool
    {
        return $this->status === self::STATUS_SIGNED;
    }
}
