<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Task\Task;
use App\Models\User\User;
use App\Notifications\TaskNotification;

class SendTaskNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     public function __construct(public Task $task) {

     }

    
    public function handle()
    {
        $user = User::find($this->task->user_id);
        if ($user) {
            $user->notify(new TaskNotification($this->task));
        }
    }
}
