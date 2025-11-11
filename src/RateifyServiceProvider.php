<?php

namespace Alhawari\Rateify;

use Illuminate\Support\ServiceProvider;

class RateifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // تحميل الـ routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // تحميل الـ views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'rateify');

        // نشر الملفات
        $this->publishes([
            __DIR__.'/config/rateify.php' => config_path('rateify.php'),
        ], 'rateify');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/rateify.php', 'rateify');
    }
}
