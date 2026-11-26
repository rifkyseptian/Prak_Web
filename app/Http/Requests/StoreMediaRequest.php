<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Rubrik validasi: judul wajib dan unik; field film/game disesuaikan dengan tipenya.
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:media,title'],
            'type' => ['required', Rule::in(['film', 'game'])],
            'creator' => ['required', 'string', 'max:255'],
            'release_year' => ['required', 'integer', 'between:1888,'.(date('Y') + 5)],
            'duration_minutes' => ['nullable', 'required_if:type,film', 'integer', 'min:1'],
            'platform' => ['nullable', 'required_if:type,game', 'string', 'max:100'],
        ];
    }
}
