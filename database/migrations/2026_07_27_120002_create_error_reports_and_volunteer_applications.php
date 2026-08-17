<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->string('page_url')->nullable();
            $table->string('error_type')->default('other');
            $table->text('description');
            $table->enum('status', ['new', 'reviewing', 'resolved'])->default('new');
            $table->timestamps();
        });

        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('work_type');
            $table->text('experience')->nullable();
            $table->enum('status', ['new', 'reviewing', 'accepted', 'rejected'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
        Schema::dropIfExists('error_reports');
    }
};
