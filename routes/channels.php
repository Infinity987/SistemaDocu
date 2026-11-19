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

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// CANAL INDIVIDUAL (Solo para Docente, Alumno y Egresado)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // dd('-----');

    if ((int) $user->id !== (int) $id) {
        return false;
    }

    $dependenciaActual = session('dependencia_id');
    // dd($dependenciaActual);
    // Docente (2), Alumno (4) o Egresado (5)
    return in_array((int) $dependenciaActual, [2, 4, 5]);
});


// 2. CANAL POR DEPENDENCIA (Para las demás dependencias administrativas)
Broadcast::channel('dependencia.{id}', function ($user, $id) {
    $dependenciaActual = session('dependencia_id');

    \Log::info('Canal privado - Autenticando', [
        'user_id' => $user->id,
        'dependencia_id' => $dependenciaActual,
        'canal_id' => $id
    ]);

    // Si la dependencia de la sesión es 2, 4 o 5, RECHAZAR inmediatamente.
    if (in_array((int) $dependenciaActual, [2, 4, 5])) {
        return false;
    }

    // Validar que la dependencia a la que intenta unirse sea la que tiene ACTIVA en sesión
    if ((int) $dependenciaActual !== (int) $id) {
        return false;
    }

    // dd('dependencia');
    // Verificar en la BD si el usuario realmente pertenece a esa dependencia
    return DB::connection('mysql_documentario')->table('dependencia_user')
        ->where('user_id', $user->id)
        ->where('dependencia_id', $id)
        ->exists();
});
