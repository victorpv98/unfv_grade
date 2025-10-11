<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseTeacher extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'teacher_id', 'period_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}