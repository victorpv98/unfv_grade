<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Period extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    public function courseStudents()
    {
        return $this->hasMany(CourseStudent::class);
    }

    public function courseTeachers()
    {
        return $this->hasMany(CourseTeacher::class);
    }
}