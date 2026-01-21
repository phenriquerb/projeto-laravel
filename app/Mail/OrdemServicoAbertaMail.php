<?php

namespace App\Mail;

use App\Models\OrdemServico;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdemServicoAbertaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public OrdemServico $ordemServico
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Ordem de Serviço {$this->ordemServico->protocolo} - Aberta",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ordem-servico-aberta',
            with: [
                'protocolo' => $this->ordemServico->protocolo,
                'cliente' => $this->ordemServico->cliente->nome,
                'equipamento' => $this->ordemServico->equipamento->tipo.' '.$this->ordemServico->equipamento->marca.' '.$this->ordemServico->equipamento->modelo,
                'atendente' => $this->ordemServico->atendente->nome,
                'relato' => $this->ordemServico->relato_cliente,
                'prioridade' => $this->ordemServico->prioridade,
                'dataAbertura' => $this->ordemServico->created_at->format('d/m/Y H:i'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
