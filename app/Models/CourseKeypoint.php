<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseKeypoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'course_id',
    ];

    // seluruh data pada coursekeypoints, dimiliki oleh course model class
    public function course() {
        return $this->belongsTo(Course::class);
    }
}
