<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class noEditarDocumento implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $dependencia_id,$tipo;

    public function __construct($dependencia_id, $tipo)
    {
        $this->dependencia_id = $dependencia_id;
        $this->tipo = $tipo;
    }

    public function broadcastOn()
    {
        \Log::info('docu recibi.' . $this->dependencia_id);
        if ($this->tipo === 'personal') {
            // Se envía al canal privado del usuario (Docente/Alumno)
            \Log::info('docu recibi para docenteeeeeeeeeeeee' . $this->dependencia_id);
            return new PrivateChannel('App.Models.User.' . $this->dependencia_id);
        }
        \Log::info('No se puede editar documento por fue recibido por la dependencia.' . $this->dependencia_id);
        return new PrivateChannel('dependencia.'.$this->dependencia_id);
    }

    public function broadcastAs()
    {
        return 'noEditarDocumento';
    }

    public function broadcastWith()
    {
        return [
            'message' => 'No se puede editar documento por fue recibido por la dependencia',
            'dependencia_id' => $this->dependencia_id,
        ];
    }
}
