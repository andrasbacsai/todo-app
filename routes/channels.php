<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function (User $user) {
    if ($user->id === Auth::id()) {
        return true;
    }

    return false;
});
