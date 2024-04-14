<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'is_active',
    ];

    // diperlukan untuk menampilkan info teacher ex. name, email, etc.
    public function user() {
        return $this->belongsTo(User::class);
    }

    // satu teacher memiliki banyak course
    public function courses(){
        return $this->hasMany(Course::class);
    }
}
