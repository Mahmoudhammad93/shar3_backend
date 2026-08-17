<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('nationality')->nullable()->after('country');
            $table->string('education_level')->nullable()->after('nationality');
            $table->string('heard_about')->nullable()->after('education_level');
            $table->boolean('works_full_time')->nullable()->after('heard_about');
            $table->boolean('participates_other_programs')->nullable()->after('works_full_time');
            $table->string('daily_hours')->nullable()->after('participates_other_programs');
            $table->timestamp('terms_accepted_at')->nullable()->after('daily_hours');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'whatsapp', 'nationality',
                'education_level', 'heard_about', 'works_full_time',
                'participates_other_programs', 'daily_hours', 'terms_accepted_at',
            ]);
        });
    }
};
