<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        session()->put('locale', $locale);

        $url = $request->headers->get('referer') ?? url()->current();

        return redirect($url);
    }
}
