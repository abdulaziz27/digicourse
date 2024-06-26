<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseVideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscribeTransactionController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/details/{course:slug}', [FrontController::class, 'details'])->name('front.details');
Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');
Route::get('/pricing', [FrontController::class, 'pricing'])->name('front.pricing');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // must logged in before create a transaction
    Route::get('/checkout', [FrontController::class, 'checkout'])->name('front.checkout')->middleware('role:student');
    Route::get('/learning/{course}/{courseVideoId}', [FrontController::class, 'learning'])->name('front.learning')->middleware('role:student|teacher|owner');
    Route::post('/checkout/store', [FrontController::class, 'checkout_store'])->name('front.checkout.store')->middleware('role:student');

    Route::prefix('admin')->name('admin.')->group(function (){
        Route::resource('categories', CategoryController::class)
        ->middleware('role:owner'); // admin.categories.index
        
        Route::resource('teachers', TeacherController::class)
        ->middleware('role:owner');

        Route::resource('courses', CourseController::class)
        ->middleware('role:owner|teacher');

        Route::resource('subscribe_transactions', SubscribeTransactionController::class)
        ->middleware('role:owner');
        
        // karena kita akan menambahkan beberapa video pada kelas terkait
        // tidak bisa pakai resource, karena kita akan melemparkan sebuah parameter id class
        // harus custom pakai get. halaman sebuah form untuk menambahkan video
        Route::get('/add/video/{course:id}', [CourseVideoController::class, 'create'])
        ->middleware('role:owner|teacher')
        ->name('course.add_video');
        
        // route yang digunakan untuk proses penyimpanan video tersebut pada kelas terkait
        Route::post('/add/video/save/{course:id}', [CourseVideoController::class, 'store'])
        ->middleware('role:owner|teacher')
        ->name('course.add_video.save');
        
        Route::resource('course_videos', CourseVideoController::class)
        ->middleware('role:owner|teacher');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
