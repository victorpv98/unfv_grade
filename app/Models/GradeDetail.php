<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_student_id',
        'practice1',
        'practice2',
        'practice3',
        'practice4',
        'midterm',
        'final',
        'substitute',
        'makeup',
    ];

    public function courseStudent()
    {
        return $this->belongsTo(CourseStudent::class);
    }
}