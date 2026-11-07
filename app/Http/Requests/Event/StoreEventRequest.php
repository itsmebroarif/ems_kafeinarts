<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:events,slug'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'venue_details' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'max_registrants' => ['nullable', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'submit_fee' => ['numeric', 'min:0'],
            'timezone' => ['integer', 'min:0'],
            'registrant_hours' => ['integer', 'min:0'],
            'delete_at' => ['integer', 'min:0'],
            'dudent_at' => ['integer', 'min:0'],
            'status' => ['string', 'max:255'],
        ];
    }
}
