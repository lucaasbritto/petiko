<?php

namespace App\Services\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\TaskNotification;

class TaskRequestService
{
    public function listTasks(Request $request)
    {
        $user = auth()->user();
        $query = Task::with('user');

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->when($request->filled('due_date'), fn($q) => $q->whereDate('due_date', $request->due_date));
        $query->when($request->filled('is_done'), fn($q) => $q->where('is_done', $request->is_done));

        return $query->orderBy('due_date')->paginate($request->get('per_page', 10));
    }

    public function createTask(array $input)
    {
        $task = Task::create($input);

        $user = User::find($task->user_id);
        if ($user) {
            $user->notify(new TaskNotification($task));
        }

        return $task;
    }

    public function updateStatus(Task $task)
    {
        $task->is_done = !$task->is_done;
        $task->save();

        return $task;
    }

    public function updateTask(Task $task, array $data)
    {
        $oldUserId = $task->user_id;
        $task->update($data);

        if (isset($data['user_id']) && $data['user_id'] != $oldUserId) {
            $newUser = User::find($data['user_id']);
            if ($newUser) {
                $newUser->notify(new TaskNotification($task));
            }
        }

        return $task;
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
        return true;
    }
}
