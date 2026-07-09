<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale', 'id'));

        Blade::directive('markdown', function ($expression) {
            return "<?php echo app_markdown($expression); ?>";
        });
    }
}
