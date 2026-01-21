<?php

namespace App\Events;

use App\Models\OrdemServico;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OsTecnicoAtribuido implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public OrdemServico $ordemServico,
        public array $funcionariosIds
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Criar canal privado para cada técnico
        return array_map(
            fn ($id) => new PrivateChannel("tecnico.{$id}"),
            $this->funcionariosIds
        );
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'os.atribuida';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'os_id' => $this->ordemServico->id,
            'protocolo' => $this->ordemServico->protocolo,
            'cliente' => $this->ordemServico->cliente->nome,
            'equipamento' => $this->ordemServico->equipamento->tipo,
            'prioridade' => $this->ordemServico->prioridade,
            'status' => $this->ordemServico->status,
        ];
    }
}
