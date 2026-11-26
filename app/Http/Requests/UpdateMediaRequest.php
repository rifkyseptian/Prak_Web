<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Rubrik validasi PATCH: field boleh parsial dan judul harus unik.
    public function rules(): array
    {
        $mediaId = $this->route('media')?->id;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('media', 'title')->ignore($mediaId)],
            'type' => ['sometimes', 'required', Rule::in(['film', 'game'])],
            'creator' => ['sometimes', 'required', 'string', 'max:255'],
            'release_year' => ['sometimes', 'required', 'integer', 'between:1888,'.(date('Y') + 5)],
            'duration_minutes' => ['nullable', 'required_if:type,film', 'integer', 'min:1'],
            'platform' => ['nullable', 'required_if:type,game', 'string', 'max:100'],
        ];
    }
}
