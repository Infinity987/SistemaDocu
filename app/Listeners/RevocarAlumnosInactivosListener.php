<?php

namespace App\Listeners;

use App\Events\MatriculaCerrada;
use App\Services\MatriculaService;
use Illuminate\Support\Facades\Log;

class RevocarAlumnosInactivosListener
{
    public function handle(MatriculaCerrada $event)
{
    Log::debug("🎧 Listener ejecutado para semestre {$event->idSemestre}");

    $servicio = new \App\Services\MatriculaService();
    $servicio->revocarAlumnosInactivos($event->idSemestre);
}

}
