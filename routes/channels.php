<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('dependencia.{id}', function ($user, $id) {
    \Log::info('Canal privado - Autenticando', [
        'user_id' => $user->id,
        'dependencia_id' => session('dependencia_id'),
        'canal_id' => $id
    ]);

    return DB::connection('mysql_documentario')->table('dependencia_user')
        ->where('user_id', $user->id)
        ->where('dependencia_id', $id)
        ->exists();
});
