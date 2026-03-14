<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\RedirectResponse;

class QrRedirectController extends Controller
{
    public function redirect(string $slug): RedirectResponse
    {
        $qrCode = QrCode::where('slug', $slug)->firstOrFail();

        if (! $qrCode->is_active) {
            if ($qrCode->fallback_url) {
                return redirect()->away($qrCode->fallback_url);
            }

            abort(404);
        }

        $qrCode->increment('scans_count');

        return redirect()->away($qrCode->destination_url);
    }
}
