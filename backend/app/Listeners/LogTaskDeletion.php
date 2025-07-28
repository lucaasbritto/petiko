<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\TaskDeleted;
use App\Jobs\LogTaskDeletionJob;

class LogTaskDeletion
{
    public function __construct()
    {
        
    }
   
    public function handle(TaskDeleted $event)
    {
        LogTaskDeletionJob::dispatch($event->task);
    }
}
