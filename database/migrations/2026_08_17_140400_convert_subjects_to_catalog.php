<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['specialization_id']);
            $table->dropUnique(['semester_id', 'slug']);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['semester_id', 'specialization_id', 'sort_order', 'is_required']);
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->unique(['semester_id', 'slug']);
        });
    }
};
