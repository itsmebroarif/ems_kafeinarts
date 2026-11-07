<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'phone', 'avatar', 'address', 'instagram', 'twitter', 'linkedin', 'github', 'website'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function committees(): HasMany
    {
        return $this->hasMany(EventCommittee::class, 'user_id');
    }

    /**
     * URL publik foto profil; null bila belum diunggah.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar || Str::startsWith($this->avatar, ['http://', 'https://', '//', 'data:'])) {
            return $this->avatar === '' ? null : $this->avatar;
        }

        return Storage::disk('public')->url($this->avatar);
    }

    /**
     * Inisial nama untuk avatar fallback.
     */
    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: ['?'];
        $letters = array_map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)), array_slice($parts, 0, 2));

        return implode('', $letters);
    }

    /**
     * @return array<int, array{platform: string, label: string, url: string, icon: string}>
     */
    public function socialLinks(): array
    {
        $links = [];

        $map = [
            'instagram' => ['Instagram', 'bi-instagram'],
            'twitter' => ['X / Twitter', 'bi-twitter-x'],
            'linkedin' => ['LinkedIn', 'bi-linkedin'],
            'github' => ['GitHub', 'bi-github'],
            'website' => ['Website', 'bi-globe'],
        ];

        foreach ($map as $field => [$label, $icon]) {
            $value = $this->{$field};

            if (! $value) {
                continue;
            }

            $links[] = [
                'platform' => $field,
                'label' => $label,
                'url' => $value,
                'icon' => $icon,
            ];
        }

        return $links;
    }
}
