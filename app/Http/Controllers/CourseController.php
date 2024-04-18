<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // mendapatkan seluruh data kelas dan menampilkannya
        // dapat diakses oleh teacher dan owner
        // kalau teacher yang akses hanya menampilkan data kelas yang dimiliki oleh teacher saat itu

        $user = Auth::user();
        $query = Course::with(['category', 'teacher', 'students'])->orderByDesc('id');

        if ($user->hasRole('teacher')) {
            $query->whereHas('teacher', function ($query) use ($user){
                $query->where('user_id', $user->id);
            });
        }

        $courses = $query->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // melemparkan juga data categories untuk mengakses datanya.
        $categories = Category::all();
        return view('admin.courses.create', compact('categories'));
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        // cek data-nya apakah yang bikin kelas seorang guru atau bukan.
        // mendapatkan data dari table teacher bukan user.
        $teacher = Teacher::where('user_id', Auth::user()->id)->first();

        if (!$teacher) {
            return redirect()->route('admin.courses.index')->withErrors('Unauthorized or invalid teacher.');
        }

        // data yang sudah dilempar dan divalidasi menggunakan custom form request
        DB::transaction(function () use ($request, $teacher){

            $validated = $request->validated();

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }else {
                $thumbnailPath = 'images/thumbnail-default.png';
            }

            $validated['slug'] = Str::slug($validated['name']);

            // dapetin id teacher
            $validated['teacher_id'] = $teacher->id;
            
            // load model dan jalankan eloquent create
            // insert data terbaru dengan data seperti diatas
            $course = Course::create($validated);

            // check apakah course_keypoints dibuat atau tidak
            if (!empty($validated['course_keypoints'])) {
                // perulangan untuk insert satu persatu
                foreach ($validated['course_keypoints'] as $keypointText) {
                    $course->course_keypoints()->create([
                        'name' => $keypointText,
                    ]);
                }
            }
        });

        return redirect()->route('admin.courses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        DB::transaction(function () use ($request, $course){

            $validated = $request->validated();

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $validated['slug'] = Str::slug($validated['name']);
            
            // load model dan jalankan eloquent create
            // insert data terbaru dengan data seperti diatas
            $course->update($validated);

            // check apakah course_keypoints dibuat atau tidak
            if (!empty($validated['course_keypoints'])) {
                // hapus keypoints yang lama
                $course->course_keypoints()->delete();
                // perulangan untuk insert satu persatu
                foreach ($validated['course_keypoints'] as $keypointText) {
                    $course->course_keypoints()->create([
                        'name' => $keypointText,
                    ]);
                }
            }
        });

        return redirect()->route('admin.courses.show', $course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        DB::beginTransaction();

        try {
            $course->delete();
            DB::commit();

            return redirect()->route('admin.courses.index');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.courses.index')->with('error', 'terjadinya sebuah error');
        }
    }
}
