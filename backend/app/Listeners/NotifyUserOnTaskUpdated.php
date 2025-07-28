<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\TaskUpdated;
use App\Jobs\SendTaskNotificationJob;

class NotifyUserOnTaskUpdated
{
    public function __construct()
    {
    
    }

    public function handle(TaskUpdated $event)
    {
         SendTaskNotificationJob::dispatch($event->task);
    }
}
