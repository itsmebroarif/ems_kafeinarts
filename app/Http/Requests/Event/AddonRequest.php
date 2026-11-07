<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class AddonRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255', 'unique:event_addons,code'],
            'slug' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_new' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_allow_buying_stack' => ['boolean'],
            'qty' => ['integer', 'min:0'],
            'max_qty' => ['integer', 'min:0'],
            'sold_qty' => ['integer', 'min:0'],
            'is_selectable_on_site' => ['boolean'],
            'is_selectable_online' => ['boolean'],
            'is_metasuggest' => ['boolean'],
            'buy_qty_step' => ['integer', 'min:0'],
            'show_qty' => ['integer', 'min:0'],
            'is_displayed' => ['boolean'],
            'is_displayed_on_backend' => ['boolean'],
            'display_type' => ['string', 'max:255'],
            'is_fit_disable' => ['boolean'],
            'order_by' => ['integer', 'min:0'],
        ];
    }
}
