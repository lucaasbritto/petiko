<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskNotification extends Notification
{
    use Queueable;

    public Task $task;
    
    public function __construct(Task $task)
    {
         $this->task = $task;
    }

    
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'description' => $this->task->description,
            'assigned_by' => auth()->user()->name ?? 'Sistema',
            'message' => 'Você recebeu uma nova tarefa: "' . $this->task->title . '"',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
