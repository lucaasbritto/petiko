<?php

namespace App\Services\Task;

use App\Models\Task\Task;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;

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

        if ($user->is_admin && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        

        return $query->orderBy('due_date')->paginate($request->get('per_page', 10));
    }

    public function createTask(array $input)
    {
        $task = Task::create($input);

        event(new TaskCreated($task));

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
        $task->update($data);

        event(new TaskUpdated($task));

        return $task;
    }

    public function deleteTask(Task $task)
    {
         $task->delete();
        event(new TaskDeleted($task));
        return true;
    }

    
    public function exportTasks()
    {
        $user = Auth::user();

        $tasks = $user->is_admin
            ? Task::with('user')->get()
            : $user->tasks()->with('user')->get();

        $csvHeader = ['ID', 'Título', 'Descrição', 'Data de Vencimento', 'Concluída', 'Responsável'];
        $rows = [];

        foreach ($tasks as $task) {
            $rows[] = [
                $task->id,
                $task->title,
                $task->description,
                $task->due_date,
                $task->is_done ? 'Sim' : 'Não',
                $task->user?->name ?? '—',
            ];
        }

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $csvHeader);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF); // Adiciona BOM para UTF-8 com acentos

        return Response::make($bom . $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="tarefas.csv"',
        ]);
    }
}
