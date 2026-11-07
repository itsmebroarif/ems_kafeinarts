<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class TicketTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:event_ticket_tiers,code'],
            'slug' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_downloadable' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_selectable_on_site' => ['boolean'],
            'is_selectable_online' => ['boolean'],
            'seat_qty' => ['integer', 'min:0'],
            'stock_qty' => ['integer', 'min:0'],
            'sold_qty' => ['integer', 'min:0'],
            'is_metasuggest' => ['boolean'],
            'qty' => ['integer', 'min:0'],
        ];
    }
}
