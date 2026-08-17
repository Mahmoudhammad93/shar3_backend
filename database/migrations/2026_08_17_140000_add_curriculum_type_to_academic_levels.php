<?php

use App\Enums\CurriculumType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_levels', function (Blueprint $table) {
            $table->string('curriculum_type', 20)->default(CurriculumType::General->value)->after('number');
        });

        if (! Schema::hasTable('academic_levels') || DB::table('academic_levels')->count() === 0) {
            return;
        }

        $legacyLevel2 = DB::table('academic_levels')->where('slug', 'level-2')->first();

        if (! $legacyLevel2) {
            DB::table('academic_levels')->update(['curriculum_type' => CurriculumType::General->value]);
            DB::table('academic_levels')->where('slug', 'specialized-level')->update([
                'curriculum_type' => CurriculumType::Specialized->value,
            ]);

            return;
        }

        DB::table('academic_levels')->where('number', 1)->update([
            'name_ar' => 'المستوى التمهيدي',
            'name_en' => 'Preparatory Level',
            'slug' => 'preparatory-level',
            'curriculum_type' => CurriculumType::General->value,
        ]);

        DB::table('academic_levels')->where('id', $legacyLevel2->id)->update([
            'number' => 3,
            'name_ar' => 'المستوى المتخصص',
            'name_en' => 'Specialized Level',
            'slug' => 'specialized-level',
            'curriculum_type' => CurriculumType::Specialized->value,
            'sort_order' => 3,
        ]);

        $level1Id = DB::table('academic_levels')->where('number', 1)->value('id');

        $advancedLevelId = DB::table('academic_levels')->insertGetId([
            'name_ar' => 'المستوى المتقدم',
            'name_en' => 'Advanced Level',
            'slug' => 'advanced-level',
            'number' => 2,
            'curriculum_type' => CurriculumType::General->value,
            'description_ar' => 'السنة الثانية والثالثة — منهج عام لجميع الطلاب.',
            'sort_order' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($level1Id) {
            DB::table('academic_years')
                ->where('academic_level_id', $level1Id)
                ->where('year_number', 2)
                ->update(['academic_level_id' => $advancedLevelId]);

            if (! DB::table('academic_years')->where('slug', 'third-year')->exists()) {
                DB::table('academic_years')->insert([
                    'academic_level_id' => $advancedLevelId,
                    'name_ar' => 'السنة الثالثة',
                    'name_en' => 'Third Year',
                    'slug' => 'third-year',
                    'year_number' => 3,
                    'sort_order' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('academic_levels', function (Blueprint $table) {
            $table->dropColumn('curriculum_type');
        });
    }
};
