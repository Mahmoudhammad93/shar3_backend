<?php

namespace App\Providers;

use App\Models\Lesson;
use App\Observers\PreserveExistingLessonPlayback;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Lesson::observe(PreserveExistingLessonPlayback::class);

        if (! app()->runningInConsole() && app()->environment('local')) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        } elseif ($appUrl = config('app.url')) {
            URL::forceRootUrl($appUrl);
        }

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('enrollment', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(20)->by($key);
        });

        RateLimiter::for('quiz', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
