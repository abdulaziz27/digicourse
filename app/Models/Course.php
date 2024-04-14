<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'path_trailer',
        'about',
        'thumbnail',
        'category_id',
        'teacher_id',
    ];

    // belongsto
    // course ini dimiliki oleh category siapa...
    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }

    // dalam satu kelas memiliki banyak video
    public function course_videos(){
        return $this->hasMany(CourseVideo::class);
    }

    // setiap kelas juga memiliki keypoints
    public function course_keypoints(){
        return $this->hasMany(CourseKeypoint::class);
    }

    // users dan courses adalah many2many
    // satu kelas bisa dimiliki banyak orang, dan satu orang bisa memiliki banyak kelas
    // sehingga, perlu bridge/junction/pivotable
    // setiap kelas memiliki banyak students

    // kamu itu punya data pengguna, tapi di dalam course_students
    public function students(){
        return $this->belongsToMany(User::class, 'course_students');
    }
}
