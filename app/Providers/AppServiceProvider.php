<?php

namespace App\Providers;

use App\LeaveDay;
use App\Page;
use App\Observers\LeaveDayObserver;
use App\Observers\PageObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Schema::defaultStringLength(191);
        LeaveDay::observe(LeaveDayObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
