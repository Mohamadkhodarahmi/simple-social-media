<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
        if (empty($this->all())) {
            dd('No data received in request');
        }
        return [
            'content' => 'required|string|max:1000',
            'file' => 'nullable|file|mimes:jpg,png,pdf|max:2048'
        ];
    }
    public function messages(): array
    {
        return [
            'content.required' => 'محتوا الزامی است.',
            'file.mimes' => 'فرمت فایل باید jpg، png یا pdf باشد.',
            'file.max' => 'حجم فایل نباید بیشتر از 2 مگابایت باشد.',
        ];
    }
}
