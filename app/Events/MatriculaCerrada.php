<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatriculaCerrada
{
    use Dispatchable, SerializesModels;

    public $idSemestre;

    public function __construct($idSemestre)
    {
        $this->idSemestre = $idSemestre;
    }
}
