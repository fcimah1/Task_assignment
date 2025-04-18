<?php

namespace App\Http\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'status' => ['required', new Enum(Status::class)],
            'user_id' =>'required|exists:users,id',
            'description' => 'string',
            'category_id' => 'required|exists:categories,id'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'status.required' => 'Status is required',
            'user_id.required' => 'User is required',
            'category_id.required' => 'Category is required',
            'user_id.exists' => 'User does not exist',
            'category_id.exists' => 'Category does not exist',
            ];
    }
}
