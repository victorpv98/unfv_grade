<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function school() { return $this->belongsTo(School::class); }
    public function prerequisites() { return $this->hasMany(CoursePrerequisite::class); }
}
