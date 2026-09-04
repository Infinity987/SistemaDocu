<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class DocumentoRecibido implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $dependencia_id, $cont_estados,$cont_fechas, $tipo;

    public function __construct($dependencia_id, $cont_estados, $tipo)
    {






        $this->dependencia_id = $dependencia_id;
        $this->cont_estados = $cont_estados;
        $this->tipo = $tipo;
        //  $this->cont_fechas =$cont_fechas;
    }

    public function broadcastOn()
    {
        \Log::info('docu recibi.' . $this->dependencia_id);
        if ($this->tipo === 'personal') {
            // Se envía al canal privado del usuario (Docente/Alumno)
            \Log::info('docu recibi para docenteeeeeeeeeeeee' . $this->dependencia_id);
            return new PrivateChannel('App.Models.User.' . $this->dependencia_id);
        }
        return new PrivateChannel('dependencia.' . $this->dependencia_id);
    }

    public function broadcastAs()
    {
        return 'DocumentoRecibido';
    }

    public function broadcastWith()
    {
        return [
            'message' => 'Nuevo documento recibido',
            'dependencia_id' => $this->dependencia_id,
            'cont_estados' => $this->cont_estados,
            // 'cont_fechas' => $this->cont_fechas,
        ];
    }
}
