<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class editarDocumento implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $dependencia_id, $cont_estados, $tipo;

    public function __construct($dependencia_id, $cont_estados, $tipo)
    {
        $this->dependencia_id = $dependencia_id;
        $this->cont_estados = $cont_estados;
        $this->tipo = $tipo;
    }

    public function broadcastOn()
    {
        \Log::info('editar documento' . $this->dependencia_id);
        if ($this->tipo === 'personal') {
            // Se envía al canal privado del usuario (Docente/Alumno)
            \Log::info('editar documento docentee...' . $this->dependencia_id);
            return new PrivateChannel('App.Models.User.' . $this->dependencia_id);
        }
        // \Log::info('Editar docu: dependencia.' . $this->dependencia_id);
        return new PrivateChannel('dependencia.' . $this->dependencia_id);
    }

    public function broadcastAs()
    {
        return 'editarDocumento';
    }

    public function broadcastWith()
    {
        return [
            'message' => 'Documento actualizado',
            'dependencia_id' => $this->dependencia_id,
            'cont_estados' => $this->cont_estados,
        ];
    }
}
