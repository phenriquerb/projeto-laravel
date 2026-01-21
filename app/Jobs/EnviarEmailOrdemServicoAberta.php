<?php

namespace App\Jobs;

use App\Mail\OrdemServicoAbertaMail;
use App\Models\OrdemServico;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class EnviarEmailOrdemServicoAberta implements ShouldQueue
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
    public function handle(): void
    {
        $os = OrdemServico::with(['cliente', 'equipamento', 'atendente'])
            ->find($this->ordemServicoId);

        if (! $os) {
            return;
        }

        Mail::to($os->cliente->email)
            ->send(new OrdemServicoAbertaMail($os));
    }
}
