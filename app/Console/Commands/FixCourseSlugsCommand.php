<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;

class FixCourseSlugsCommand extends Command
{
    protected $signature = 'courses:fix-slugs {--dry-run : Preview slug changes without saving}';

    protected $description = 'Regenerate unique course slugs from Arabic titles';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;

        Course::query()->orderBy('id')->each(function (Course $course) use ($dryRun, &$updated) {
            $slug = Course::generateUniqueSlug($course->title_ar, $course->id);

            if ($course->slug === $slug) {
                return;
            }

            $this->line(sprintf(
                'Course #%d: %s -> %s',
                $course->id,
                $course->slug,
                $slug,
            ));

            if (! $dryRun) {
                $course->forceFill(['slug' => $slug])->saveQuietly();
            }

            $updated++;
        });

        $this->info($dryRun
            ? "Would update {$updated} course slug(s)."
            : "Updated {$updated} course slug(s).");

        return self::SUCCESS;
    }
}
