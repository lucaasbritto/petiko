<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Task\Task;
use App\Observers\TaskObserver;

class AppServiceProvider extends ServiceProvider
{
    
    public function register()
    {
        //
    }

    
    public function boot()
    {
        Task::observe(TaskObserver::class);
    }
}
