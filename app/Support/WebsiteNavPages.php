<?php

namespace App\Support;

class WebsiteNavPages
{
    /** @return array<int, array<string, mixed>> */
    public static function defaults(): array
    {
        return [
            [
                'href' => '/',
                'label_ar' => 'الرئيسية',
                'label_en' => 'Home',
                'description_ar' => null,
                'description_en' => null,
                'show_in_footer' => false,
                'is_visible' => true,
            ],
            [
                'href' => '/about',
                'label_ar' => 'عن المعهد',
                'label_en' => 'About',
                'description_ar' => 'تعليم العلوم الشرعية على منهج أهل السنة والجماعة',
                'description_en' => 'Islamic education upon the methodology of Ahl al-Sunnah',
                'show_in_footer' => true,
                'is_visible' => true,
            ],
            [
                'href' => '/study-plan',
                'label_ar' => 'الخطة الدراسية',
                'label_en' => 'Study Plan',
                'description_ar' => 'منهج علمي متدرّج يجمع بين الحفظ والمتون الأساسية والكتب التكميلية في العلوم الشرعية',
                'description_en' => 'A structured curriculum in the Islamic sciences',
                'show_in_footer' => true,
                'is_visible' => true,
            ],
            [
                'href' => '/courses',
                'label_ar' => 'الدورات',
                'label_en' => 'Courses',
                'description_ar' => 'دورات معتمدة في القرآن والتفسير والفقه والحديث والعقيدة — على منهج أهل السنة والجماعة',
                'description_en' => 'Courses in Quran, Tafsir, Fiqh, Hadith, and Aqeedah',
                'show_in_footer' => true,
                'is_visible' => true,
            ],
            [
                'href' => '/programs',
                'label_ar' => 'المقررات الدراسية',
                'label_en' => 'Programs',
                'description_ar' => 'مسارات متدرجة على خمس سنوات من التأسيس إلى التأصيل ثم التخصص',
                'description_en' => 'Five-year academic programs from foundation to specialization',
                'show_in_footer' => true,
                'is_visible' => true,
            ],
            [
                'href' => '/regulations',
                'label_ar' => 'اللائحة التنظيمية',
                'label_en' => 'Regulations',
                'description_ar' => 'ضوابط وأحكام تنظّم العلاقة بين المعهد وطلابه لضمان بيئة علمية محترمة',
                'description_en' => 'Institute regulations and student guidelines',
                'show_in_footer' => true,
                'is_visible' => true,
            ],
            [
                'href' => '/teachers',
                'label_ar' => 'المعلمون',
                'label_en' => 'Teachers',
                'description_ar' => 'نخبة من أهل العلم والاختصاص في التفسير والفقه والحديث والعقيدة',
                'description_en' => 'Scholars and instructors in the Islamic sciences',
                'show_in_footer' => true,
                'is_visible' => true,
            ],
            [
                'href' => '/news',
                'label_ar' => 'الأخبار',
                'label_en' => 'News',
                'description_ar' => 'آخر مستجدات المعهد وأخبار البرامج والدورات الشرعية',
                'description_en' => 'Latest institute news and announcements',
                'show_in_footer' => true,
                'is_visible' => true,
            ],
            [
                'href' => '/contact',
                'label_ar' => 'تواصل معنا',
                'label_en' => 'Contact',
                'description_ar' => 'نسعد باستقبال استفساراتكم حول الدورات والبرامج الشرعية والتسجيل في المعهد',
                'description_en' => 'Contact us about courses, programs, and registration',
                'show_in_footer' => true,
                'is_visible' => true,
            ],
        ];
    }

    /** @param  array<int, array<string, mixed>>|null  $stored */
    public static function resolve(?array $stored): array
    {
        $defaults = collect(self::defaults())->keyBy('href');
        $storedCollection = collect($stored ?? [])
            ->filter(fn ($item) => is_array($item) && ! empty($item['href']))
            ->values();

        if ($storedCollection->isEmpty()) {
            return self::withSortOrder(self::defaults());
        }

        $storedCollection = $storedCollection
            ->sortBy(fn ($item, int $index) => array_key_exists('sort_order', $item) && $item['sort_order'] !== null
                ? (int) $item['sort_order']
                : $index)
            ->values();

        $ordered = [];
        $seen = [];

        foreach ($storedCollection as $index => $item) {
            $href = self::normalizeHref((string) $item['href']);
            $default = $defaults->get($href);

            if ($default === null) {
                continue;
            }

            $ordered[] = self::mergeItem($default, $item, $index);
            $seen[] = $href;
        }

        foreach ($defaults as $href => $default) {
            if (! in_array($href, $seen, true)) {
                $ordered[] = self::mergeItem($default, $default, count($ordered));
            }
        }

        return self::withSortOrder($ordered);
    }

    /** @param  array<string, mixed>  $stored */
    public static function normalizeForStorage(array $stored): array
    {
        $defaults = collect(self::defaults())->keyBy('href');
        $ordered = [];

        foreach ($stored as $index => $item) {
            if (! is_array($item) || empty($item['href'])) {
                continue;
            }

            $href = self::normalizeHref((string) $item['href']);
            $default = $defaults->get($href);

            if ($default === null) {
                continue;
            }

            $ordered[] = self::mergeItem($default, $item, $index);
        }

        $seen = collect($ordered)->pluck('href')->all();

        foreach ($defaults as $href => $default) {
            if (! in_array($href, $seen, true)) {
                $ordered[] = self::mergeItem($default, $default, count($ordered));
            }
        }

        return self::withSortOrder($ordered);
    }

    /** @return array<string, mixed>|null */
    public static function findByPath(?array $stored, string $path): ?array
    {
        $normalizedPath = self::normalizeHref($path);

        foreach (self::resolve($stored) as $page) {
            if (self::normalizeHref((string) $page['href']) === $normalizedPath) {
                return $page;
            }
        }

        return null;
    }

    public static function normalizeHref(string $href): string
    {
        $trimmed = trim($href);

        if ($trimmed === '' || $trimmed === '/') {
            return '/';
        }

        return '/'.trim($trimmed, '/');
    }

    /** @param  array<int, array<string, mixed>>  $pages
     * @return array<int, array<string, mixed>>
     */
    private static function withSortOrder(array $pages): array
    {
        return array_values(array_map(
            fn (array $page, int $index) => [...$page, 'sort_order' => $index],
            $pages,
            array_keys($pages),
        ));
    }

    /** @param  array<string, mixed>  $default
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private static function mergeItem(array $default, array $item, int $sortOrder): array
    {
        return [
            'href' => self::normalizeHref((string) ($item['href'] ?? $default['href'])),
            'label_ar' => trim((string) ($item['label_ar'] ?? '')) ?: $default['label_ar'],
            'label_en' => trim((string) ($item['label_en'] ?? '')) ?: ($default['label_en'] ?? null),
            'description_ar' => array_key_exists('description_ar', $item)
                ? (trim((string) ($item['description_ar'] ?? '')) ?: null)
                : ($default['description_ar'] ?? null),
            'description_en' => array_key_exists('description_en', $item)
                ? (trim((string) ($item['description_en'] ?? '')) ?: null)
                : ($default['description_en'] ?? null),
            'show_in_footer' => array_key_exists('show_in_footer', $item)
                ? (bool) $item['show_in_footer']
                : (bool) ($default['show_in_footer'] ?? true),
            'is_visible' => self::normalizeHref((string) ($item['href'] ?? $default['href'])) === '/'
                ? true
                : (array_key_exists('is_visible', $item)
                    ? (bool) $item['is_visible']
                    : (bool) ($default['is_visible'] ?? true)),
            'sort_order' => $sortOrder,
        ];
    }
}
