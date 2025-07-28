<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'due_date'    => 'required|date|after_or_equal:today',
            'user_id'     => 'required|integer|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required'          => 'O Titulo é obrigatório.',
            'description.required'    => 'A Descrição é obrigatória.',
            'due_date.required'       => 'A Data de Vencimento é obrigatória.',
            'due_date.after_or_equal' => 'A Data de Vencimento deve ser hoje ou uma data futura.',
            'user_id.required'        => 'O Responsável é Obrigatório',
        ];
    }
}
