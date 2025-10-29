<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseActaFile extends Model
{
    use HasFactory;

    public const TYPE_GENERATED = 'generated';
    public const TYPE_UPLOADED = 'uploaded';

    protected $fillable = [
        'course_acta_id',
        'version',
        'type',
        'disk',
        'path',
        'filename',
        'mime_type',
        'file_size',
        'checksum',
        'uploaded_by',
        'uploaded_at',
        'metadata',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function acta(): BelongsTo
    {
        return $this->belongsTo(CourseActa::class, 'course_acta_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isGenerated(): bool
    {
        return $this->type === self::TYPE_GENERATED;
    }

    public function isUploaded(): bool
    {
        return $this->type === self::TYPE_UPLOADED;
    }
}
