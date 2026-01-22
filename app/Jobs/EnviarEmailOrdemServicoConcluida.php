<?php

namespace App\Jobs;

use App\Application\Services\OrdemServicoPdfService;
use App\Mail\OrdemServicoConcluidaMail;
use App\Models\OrdemServico;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class EnviarEmailOrdemServicoConcluida implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $ordemServicoId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OrdemServicoPdfService $pdfService): void
    {
        $os = OrdemServico::with([
            'cliente',
            'equipamento',
            'atendente',
            'responsaveis',
            'evidencias',
        ])->find($this->ordemServicoId);

        if (! $os) {
            return;
        }

        $pdfPath = $pdfService->gerarPdf($os);

        Mail::to($os->cliente->email)
            ->send(new OrdemServicoConcluidaMail($os, $pdfPath));

        // Storage::delete($pdfPath);
    }
}
