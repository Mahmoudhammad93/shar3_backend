@php
    /** @var \Filament\Forms\Components\ViewField|null $field */
    $record = isset($field) ? $field->getRecord() : null;
    $statePath = isset($field) ? ($field->getStatePath() ?? 'data.bunny_uploader') : 'data.bunny_uploader';
    $formStatePath = str_contains($statePath, '.')
        ? substr($statePath, 0, strrpos($statePath, '.'))
        : 'data';
    $lessonId = $record?->getKey();
    $videoId = $record?->bunny_video_id;
    $libraryId = $record?->bunny_library_id;
    $status = $record?->bunny_status;
    $statusLabel = $videoId
        ? (\App\Enums\LessonBunnyStatus::tryFrom((string) $status)?->labelAr() ?? ($status ?: 'لم يتم الرفع بعد'))
        : 'لم يتم الرفع بعد';
@endphp

<div
    class="space-y-4"
    x-data="bunnyLessonUploader({{ \Illuminate\Support\Js::from([
        'lessonId' => $lessonId,
        'formStatePath' => $formStatePath,
        'videoId' => $videoId,
        'libraryId' => $libraryId,
        'statusLabel' => $statusLabel,
    ]) }})"
>
    <div>
        <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">
            <span x-text="videoId ? 'استبدال الفيديو' : 'اختر ملف الفيديو'">اختر ملف الفيديو</span>
        </label>
        <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-900 dark:hover:bg-gray-800">
            <span class="text-sm font-semibold text-primary-600">اختر ملف الفيديو</span>
            <span class="text-xs text-gray-500">MP4 / WebM / MOV — حتى 8 غيغابايت</span>
            <span class="text-xs text-gray-500" x-show="fileName" x-text="fileName"></span>
            <input
                type="file"
                accept=".mp4,.webm,.mov,video/mp4,video/webm,video/quicktime"
                class="sr-only"
                @change="onFileSelected($event)"
                :disabled="uploading"
            />
        </label>
        <p class="mt-2 text-xs text-gray-500">
            الملف يُرسل عبر خادم Laravel إلى Bunny Stream ولا يُحفظ تخزيناً دائماً. لن يُرفع شيء إلا إذا اخترت ملفاً جديداً.
        </p>
    </div>

    <div class="rounded-lg bg-gray-50 px-3 py-2 text-sm dark:bg-gray-900">
        <span class="font-medium">حالة الفيديو:</span>
        <span x-text="statusLabel">{{ $statusLabel }}</span>
    </div>

    <div class="text-sm text-gray-600 dark:text-gray-300" x-show="videoId">
        <span class="font-medium">Bunny Video ID:</span>
        <span class="font-mono" x-text="videoId"></span>
    </div>

    <div class="flex flex-wrap gap-2">
        <button type="button" class="fi-btn fi-size-sm" @click="refreshStatus()" :disabled="uploading || !lessonId">
            تحديث الحالة
        </button>
        <button type="button" class="fi-btn fi-size-sm" @click="removeVideo()" x-show="!lessonId && videoId" :disabled="uploading">
            إلغاء الرفع غير المحفوظ
        </button>
    </div>

    <div x-show="uploading" class="text-sm text-gray-600">
        جاري الرفع إلى Bunny… <span x-text="progress + '%'"></span>
    </div>
    <p class="text-sm text-primary-600" x-show="message" x-text="message"></p>
</div>
