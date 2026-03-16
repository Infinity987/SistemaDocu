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

    public $dependencia_id, $cont_estados;

    public function __construct($dependencia_id, $cont_estados)
    {
        $this->dependencia_id = $dependencia_id;
        $this->cont_estados = $cont_estados;
    }

    public function broadcastOn()
    {
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
