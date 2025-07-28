<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\TaskCreated;
use App\Jobs\SendTaskNotificationJob;

class NotifyUserOnTaskCreated
{    
    public function __construct()
    {
        
    }

    public function handle(TaskCreated $event)
    {
         SendTaskNotificationJob::dispatch($event->task);
    }
}
