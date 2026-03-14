<?php

namespace App\Policies;

use App\Models\QrCode;
use App\Models\User;

class QrCodePolicy
{
    /**
     * Admins can view any QR code. Clients can only view their own.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, QrCode $qrCode): bool
    {
        return $user->isAdmin() || $qrCode->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, QrCode $qrCode): bool
    {
        return $user->isAdmin() || $qrCode->user_id === $user->id;
    }

    public function delete(User $user, QrCode $qrCode): bool
    {
        return $user->isAdmin() || $qrCode->user_id === $user->id;
    }

    public function download(User $user, QrCode $qrCode): bool
    {
        return $user->isAdmin() || $qrCode->user_id === $user->id;
    }
}
