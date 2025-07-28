<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskOverdueNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => "Tarefa Vencida",
            'message' => "A tarefa {$this->task->id} está vencida desde {$this->task->due_date->format('d/m/Y')}.",
            'task_id' => $this->task->id,
            'type' => "warning"
        ];
    }
}
