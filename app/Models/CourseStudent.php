<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    public function student() { return $this->belongsTo(Student::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function period() { return $this->belongsTo(Period::class); }
    public function gradeDetail() { return $this->hasOne(GradeDetail::class); }
    public function finalGrade() { return $this->hasOne(FinalGrade::class); }
}
