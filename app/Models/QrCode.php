<?php

namespace App\Models;

use Database\Factories\QrCodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QrCode extends Model
{
    /** @use HasFactory<QrCodeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'destination_url',
        'fallback_url',
        'is_active',
        'scans_count',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'scans_count' => 'integer',
        ];
    }

    public static function generateUniqueSlug(): string
    {
        do {
            $slug = Str::random(8);
        } while (self::where('slug', $slug)->exists());

        return $slug;
    }

    public function getRedirectUrl(): string
    {
        return route('qr.redirect', $this->slug);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
