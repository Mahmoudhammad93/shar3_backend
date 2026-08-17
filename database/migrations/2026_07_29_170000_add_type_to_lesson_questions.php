<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_questions', function (Blueprint $table) {
            $table->string('type')->default('choice')->after('question_ar');
            $table->string('correct_answer')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'correct_answer']);
        });
    }
};
