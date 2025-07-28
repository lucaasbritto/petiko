<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskOverdueNotification;
use Carbon\Carbon;

class NotifyOverdueTasks extends Command
{
    protected $signature = 'tasks:notify-overdue';
    protected $description = 'Notifica os usuários sobre tarefas vencidas e pendentes';
    
    public function handle(){
       $now = Carbon::now();

        $tasks = Task::with('user')
            ->where('is_done', false)
            ->whereDate('due_date', '<', $now)
            ->get();

        $notified = 0;

        foreach ($tasks as $task) {
            if ($task->user) {
                $task->user->notify(new TaskOverdueNotification($task));
                $notified++;
            }
        }

        $this->info("$notified tarefas vencidas notificadas com sucesso.");
    }    
}
