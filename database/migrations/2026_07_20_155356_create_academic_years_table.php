<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_level_id')->constrained('academic_levels')->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('slug');
            $table->unsignedTinyInteger('year_number');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['academic_level_id', 'slug']);
            $table->unique(['academic_level_id', 'year_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
