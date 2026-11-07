<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'guests_quantity' => ['nullable', 'string', 'max:255'],
            'registrant_type' => ['nullable', 'string', 'max:255'],
            'guests_quantity_other' => ['nullable', 'string', 'max:255'],
            'ticket_tier_id' => ['required', 'exists:event_ticket_tiers,id'],
            'voucher_code' => ['nullable', 'string', 'max:255'],
            'addons' => ['array'],
            'addons.*' => ['integer', 'min:0'],
            'custom_fields' => ['array'],
            'custom_fields.*' => ['string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->has('ticket_tier_id')) {
                $validator->errors()->add('ticket_tier_id', 'Ticket tier is required.');
            }
        });
    }
}
