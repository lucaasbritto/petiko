<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Task\Task;
use Illuminate\Support\Facades\Log;

class LogTaskDeletionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   
    public function __construct(public Task $task)
    {
        
    }

    
    public function handle()
    {
        Log::info("Tarefa deletada: {$this->task->id} - {$this->task->title}");
    }
}
