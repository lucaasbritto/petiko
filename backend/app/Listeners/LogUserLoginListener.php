<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use App\Events\UserLoggedIn;
use App\Jobs\LogUserLoginJob;

class LogUserLoginListener
{
    public function __construct()
    {
        //
    }

    
    public function handle(UserLoggedIn $event)
    {        
        $ip = Request::ip();
        LogUserLoginJob::dispatch($event->user, $ip);
    }
}
