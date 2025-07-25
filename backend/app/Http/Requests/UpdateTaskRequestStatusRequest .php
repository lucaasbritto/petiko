<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TaskRequest;

class UpdateTaskRequestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'is_done' => 'required|in:boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $requestModel = TaskRequest::find($this->route('id'));

            if (!$requestModel) {
                $validator->errors()->add('id', 'Tarefa não encontrada.');
                return;
            }

        });
    }
}