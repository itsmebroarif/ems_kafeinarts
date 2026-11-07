<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
            'code' => ['required', 'string', 'max:255', 'unique:event_vouchers,code'],
            'is_percent' => ['boolean'],
            'value' => ['required', 'numeric', 'min:0'],
            'is_single_use' => ['boolean'],
            'is_active' => ['boolean'],
            'total_uses' => ['integer', 'min:0'],
            'uses' => ['integer', 'min:0'],
            'is_auto_apply' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_show_on_front' => ['boolean'],
            'is_show_on_payment' => ['boolean'],
            'expired_at' => ['nullable', 'date'],
            'order_by' => ['integer', 'min:0'],
        ];
    }
}
