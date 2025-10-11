<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseStudent extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'student_id', 'period_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function gradeDetail()
    {
        return $this->hasOne(GradeDetail::class);
    }

    public function finalGrade()
    {
        return $this->hasOne(FinalGrade::class);
    }
}