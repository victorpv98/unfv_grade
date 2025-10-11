<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinalGrade extends Model
{
    use HasFactory;

    protected $fillable = ['course_student_id', 'average', 'status'];

    public function courseStudent()
    {
        return $this->belongsTo(CourseStudent::class);
    }
}