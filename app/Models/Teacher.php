<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'specialty'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseTeachers()
    {
        return $this->hasMany(CourseTeacher::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_teachers')
                    ->withTimestamps();
    }
}