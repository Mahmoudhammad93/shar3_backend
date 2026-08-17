<?php

use App\Enums\StudentSpecializationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_specializations', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropUnique(['student_id']);
        });

        Schema::table('student_specializations', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->string('status', 20)
                ->default(StudentSpecializationStatus::Active->value)
                ->after('specialization_id');
            $table->renameColumn('chosen_at', 'selected_at');
            $table->unique(['student_id', 'specialization_id']);
        });
    }

    public function down(): void
    {
        Schema::table('student_specializations', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropUnique(['student_id', 'specialization_id']);
        });

        Schema::table('student_specializations', function (Blueprint $table) {
            $table->renameColumn('selected_at', 'chosen_at');
            $table->dropColumn('status');
            $table->unique('student_id');
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });
    }
};
