<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseThumbnailSeeder extends Seeder
{
    /** Reliable placeholder thumbnails (placehold.co). */
    private const THUMBNAILS = [
        'tafsir-al-baqarah' => 'https://placehold.co/800x500/0a3d34/c9a227/png?text=Tafsir',
        'fiqh-ibadat' => 'https://placehold.co/800x500/004d40/ffffff/png?text=Fiqh',
        'ulum-al-hadith' => 'https://placehold.co/800x500/78350f/ffffff/png?text=Hadith',
        'islamic-aqeedah' => 'https://placehold.co/800x500/1e293b/c9a227/png?text=Aqeedah',
    ];

    public function run(): void
    {
        foreach (self::THUMBNAILS as $slug => $url) {
            Course::query()->where('slug', $slug)->update(['image' => $url]);
        }

        Course::query()
            ->where(function ($query) {
                $query->whereNull('image')->orWhere('image', '');
            })
            ->each(function (Course $course) {
                $course->update([
                    'image' => 'https://placehold.co/800x500/004d40/ffffff/png?text=Course',
                ]);
            });
    }
}
