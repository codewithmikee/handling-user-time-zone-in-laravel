<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: IF ANYONE NEEDS TO CHECK IF THE USER CAN CREATE A POST, USE THIS
        // $this->user()->can('create', Post::class);
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
        ];
    }
}
