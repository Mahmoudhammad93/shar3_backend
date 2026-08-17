<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['student_id', 'status']);
            $table->index(['course_id', 'status']);
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->index(['student_id', 'is_completed']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index(['is_published', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'status']);
            $table->dropIndex(['course_id', 'status']);
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'is_completed']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'slug']);
        });
    }
};
