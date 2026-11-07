<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'guests_quantity' => ['nullable', 'string', 'max:255'],
            'registrant_type' => ['nullable', 'string', 'max:255'],
            'guests_quantity_other' => ['nullable', 'string', 'max:255'],
            'ticket_tier_id' => ['sometimes', 'required', 'exists:event_ticket_tiers,id'],
            'voucher_code' => ['nullable', 'string', 'max:255'],
            'addons' => ['array'],
            'addons.*' => ['integer', 'min:0'],
            'custom_fields' => ['array'],
            'custom_fields.*' => ['string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:pending,paid,confirmed,rejected,cancelled,expired'],
            'is_paid' => ['sometimes', 'boolean'],
            'is_confirmed' => ['sometimes', 'boolean'],
            'is_rejected' => ['sometimes', 'boolean'],
            'is_pending' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
            'shipping_method' => ['nullable', 'string', 'max:255'],
        ];
    }
}
