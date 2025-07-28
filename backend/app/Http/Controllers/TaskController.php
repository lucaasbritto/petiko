<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskRequestStatusRequest;
use App\Notifications\TaskNotification;

class TaskController extends Controller
{
    
    public function index(Request $request){
       
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
        $query->when($request->filled('due_date'), fn($q) => $q->whereDate('due_date', '=', $request->due_date));
        $query->when($request->filled('is_done'), fn($q) => $q->where('is_done', $request->is_done));


        $perPage = $request->get('per_page', 10);
        $requests = $query->orderBy('due_date')->paginate($perPage);

        return response()->json($requests);
    }
    
       
    public function store(TaskRequest $request){
       $input = $request->validated();
       $task = Task::create($input);

        $user = User::find($task->user_id);
        if ($user) {
            $user->notify(new TaskNotification($task));
        }

       return response()->json([
            'message' => 'Nova tarefa criada com sucesso.',
            'data'    => $task,
        ], 201);
    }

    public function updateStatus($id){

        $task = Task::where('user_id', auth()->id())->findOrFail($id);
        $task->is_done = !$task->is_done;
        $task->save();

        return response()->json([
            'message' => 'Status atualizado com sucesso',
            'data' => $task
        ]);
    }
    
    public function update(UpdateTaskRequest $request, Task $task){
        if ($task->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $validatedData = $request->validated();

        $oldUserId = $task->user_id;

        $task->update($validatedData);

        if (isset($validatedData['user_id']) && $validatedData['user_id'] != $oldUserId) {
            $newUser = User::find($validatedData['user_id']);
            if ($newUser) {
                $newUser->notify(new TaskNotification($task));
            }
        }

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
        $task->delete();

        return response()->json(['message' => 'Tarefa removida com sucesso']);
    }
}
