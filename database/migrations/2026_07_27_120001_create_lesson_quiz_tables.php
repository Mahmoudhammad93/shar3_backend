<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('question_ar');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lesson_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_question_id')->constrained()->cascadeOnDelete();
            $table->string('option_ar');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->boolean('quiz_passed')->default(false)->after('progress_percent');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropColumn('quiz_passed');
        });

        Schema::dropIfExists('lesson_question_options');
        Schema::dropIfExists('lesson_questions');
    }
};
