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

    public $dependencia_id;

    public function __construct($dependencia_id)
    {
        $this->dependencia_id = $dependencia_id;
    }

    public function broadcastOn()
    {
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
