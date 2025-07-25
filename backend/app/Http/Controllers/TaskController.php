<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequestStatusRequest;

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
       $task = Task::create([
            'title'       => $input['title'],
            'description' => $input['description'],
            'due_date'    => $input['due_date'],
            'user_id'     => $input['user_id']
        ]);

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
    
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    
    public function update(Request $request, Task $task){
        if ($task->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'is_done' => 'required|boolean',
        ]);

        $task->update($validatedData);

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
