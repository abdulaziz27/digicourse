<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    // cara pertama dalam mempersiapkan mass assigment  
    // plus: safer
    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    // cara kedua
    // minus: user dapat memasukan apa saja yang membahayakan sistem (hindari adanya data sensitif: password, etc)
    protected $guarded = [
        'id',
    ];

    // many to one
    // satu category dapat memiliki banyak course
    // satu course dapat memiliki satu category
    public function courses(){
        return $this->hasMany(Course::class);
    }
}
