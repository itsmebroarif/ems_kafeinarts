<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:event_categories,name'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_sport' => ['nullable', 'boolean'],
        ], [
            'category_name.unique' => 'Kategori tersebut sudah ada.',
            'category_name.required' => 'Nama kategori wajib diisi.',
            'color.regex' => 'Format warna harus berupa heksadesimal, contoh #0d6efd.',
        ]);

        $category = EventCategory::create([
            'name' => $validated['category_name'],
            'slug' => $this->uniqueSlug($validated['category_name']),
            'color' => $validated['color'] ?? '#0d6efd',
            'is_active' => true,
            'is_sport' => $request->boolean('is_sport'),
        ]);

        return redirect()
            ->back()
            ->with('success', "Kategori \"{$category->name}\" berhasil ditambahkan.")
            ->with('selected_category', $category->id);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (EventCategory::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
