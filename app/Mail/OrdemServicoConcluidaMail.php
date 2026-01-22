<?php

namespace App\Mail;

use App\Models\OrdemServico;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdemServicoConcluidaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public OrdemServico $ordemServico,
        public string $pdfPath
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Ordem de Serviço {$this->ordemServico->protocolo} - Concluída",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ordem-servico-concluida',
            with: [
                'protocolo' => $this->ordemServico->protocolo,
                'cliente' => $this->ordemServico->cliente->nome,
                'equipamento' => $this->ordemServico->equipamento->tipo.' '.
                    $this->ordemServico->equipamento->marca.' '.
                    $this->ordemServico->equipamento->modelo,
                'valorTotal' => 'R$ '.number_format($this->ordemServico->valor_total, 2, ',', '.'),
                'dataConclusao' => $this->ordemServico->data_conclusao->format('d/m/Y H:i'),
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
        return [
            Attachment::fromPath($this->pdfPath)
                ->as("OS-{$this->ordemServico->protocolo}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
