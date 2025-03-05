<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:500'
        ];
    }
    public function messages(): array
    {
        return [
            'post_id.required' => 'شناسه پست الزامی است.',
            'post_id.exists' => 'پست مورد نظر وجود ندارد.',
            'content.required' => 'متن کامنت الزامی است.',
        ];
    }
}
