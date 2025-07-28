<?php

namespace App\Observers;

use App\Models\Task\Task;
use App\Models\Task\TaskHistoryDeleted;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    
    public function created(Task $task)
    {
        //
    }

    
    public function updated(Task $task)
    {
        //
    }

    
    public function deleted(Task $task)
    {
        TaskHistoryDeleted::create([
            'task_id'     => $task->id,
            'title'       => $task->title,
            'description' => $task->description,
            'due_date'    => $task->due_date,
            'is_done'     => $task->is_done,
            'deleted_by'  => Auth::id(),
        ]);
    }

    
    public function restored(Task $task)
    {
        //
    }

    
    public function forceDeleted(Task $task)
    {
        //
    }
}
