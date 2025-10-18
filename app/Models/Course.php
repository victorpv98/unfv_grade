<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'code', 'name', 'credits'];

    // Relaciones
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function prerequisites()
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'course_id',
            'prerequisite_id'
        );
    }

    public function prerequisiteLinks()
    {
        return $this->hasMany(CoursePrerequisite::class);
    }

    public function courseTeachers()
    {
        return $this->hasMany(CourseTeacher::class);
    }

    public function courseStudents()
    {
        return $this->hasMany(CourseStudent::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'course_teachers')
                    ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_students')
                    ->withTimestamps();
    }
}
