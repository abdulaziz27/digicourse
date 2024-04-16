<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // migrate fresh: migrasi ulang (cocok kalo baru mulai)
        
        // to rollback
        // php artisan migrate:status, cek step. cms e4.
        Schema::create('course_videos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path_video');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->softDeletes(); // deleted_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_videos');
    }
};
