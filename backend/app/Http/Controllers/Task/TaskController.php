<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task\Task;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Notifications\TaskNotification;
use App\Services\Task\TaskRequestService;


class TaskController extends Controller
{
     protected $service;

    public function __construct(TaskRequestService $service)
    {
        $this->service = $service;
    }
    
    public function index(Request $request){
        $tasks = $this->service->listTasks($request);
        return response()->json($tasks);

    }
    
       
    public function store(TaskRequest $request){
       $task = $this->service->createTask($request->validated());

        return response()->json([
            'message' => 'Nova tarefa criada com sucesso.',
            'data'    => $task,
        ], 201);
    }

    public function updateStatus($id){

        $task = Task::where('user_id', auth()->id())->findOrFail($id);
        $task = $this->service->updateStatus($task);

        return response()->json([
            'message' => 'Status atualizado com sucesso',
            'data' => $task
        ]);
    }
    
    public function update(UpdateTaskRequest $request, Task $task){
        if ($task->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $task = $this->service->updateTask($task, $request->validated());

        return response()->json([
            'message' => 'Tarefa atualizada com sucesso',
            'data' => $task
        ]);
    }


    public function destroy(Task $task){
        $user = auth()->user();

        if ($task->user_id !== $user->id && !$user->is_admin) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $this->service->deleteTask($task);

        return response()->json(['message' => 'Tarefa removida com sucesso']);
    }
}
