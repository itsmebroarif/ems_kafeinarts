<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan form edit profil milik pengguna yang sedang login.
     */
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Perbarui profil: identitas, kontak, sosial media, dan foto.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'github' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['boolean'],
        ]);

        $validated['instagram'] = $this->normalizeSocial($validated['instagram'] ?? null, 'instagram');
        $validated['twitter'] = $this->normalizeSocial($validated['twitter'] ?? null, 'twitter');
        $validated['linkedin'] = $this->normalizeSocial($validated['linkedin'] ?? null, 'linkedin');
        $validated['github'] = $this->normalizeSocial($validated['github'] ?? null, 'github');
        $validated['website'] = $this->normalizeWebsite($validated['website'] ?? null);

        // Unggah avatar baru (atau hapus).
        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user->avatar);
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($user->avatar);
            $validated['avatar'] = null;
        } else {
            unset($validated['avatar']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ganti password pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Perbarui avatar yang disimpan sebagai URL (web) tanpa upload ulang.
     */
    private function normalizeSocial(?string $value, string $platform): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Jika hanya username saja, normalisasi menjadi URL lengkap.
        if (! Str::startsWith($value, ['http://', 'https://', '//'])) {
            $handle = ltrim($value, '@');

            return match ($platform) {
                'instagram' => 'https://instagram.com/'.$handle,
                'twitter' => 'https://x.com/'.$handle,
                'linkedin' => 'https://linkedin.com/in/'.$handle,
                'github' => 'https://github.com/'.$handle,
                default => $value,
            };
        }

        return $value;
    }

    private function normalizeWebsite(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (! Str::startsWith($value, ['http://', 'https://'])) {
            return 'https://'.$value;
        }

        return $value;
    }

    private function deleteAvatar(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
