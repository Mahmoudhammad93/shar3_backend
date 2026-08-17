<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['semester_id', 'subject_id', 'specialization_id'], 'curriculum_assignments_unique');
        });

        if (Schema::hasColumn('subjects', 'semester_id')) {
            $subjects = DB::table('subjects')->get();

            foreach ($subjects as $subject) {
                if ($subject->semester_id === null) {
                    continue;
                }

                DB::table('curriculum_assignments')->insert([
                    'semester_id' => $subject->semester_id,
                    'subject_id' => $subject->id,
                    'specialization_id' => $subject->specialization_id,
                    'sort_order' => $subject->sort_order ?? 0,
                    'is_required' => $subject->is_required ?? true,
                    'is_active' => $subject->is_active ?? true,
                    'created_at' => $subject->created_at ?? now(),
                    'updated_at' => $subject->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_assignments');
    }
};
