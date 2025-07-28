<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'required|date',
            'is_done'     => 'required|boolean',
            'user_id'     => 'required|integer|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required'       => 'O Titulo é obrigatório.',
            'description.required' => 'A Descrição é obrigatória.',
            'due_date.required'    => 'A Data de Vencimento é obrigatória.',            
            'is_done.required'     => 'O status é obrigatório.',
            'user_id.required'     => 'O Responsável é Obrigatório',
        ];
    }
}
