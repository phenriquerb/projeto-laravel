<?php

namespace App\Application\Services;

use App\Models\OrdemServico;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class OrdemServicoPdfService
{
    /**
     * Gera o PDF de uma ordem de serviço e salva no storage
     *
     * @return string Caminho completo do arquivo PDF no storage
     */
    public function gerarPdf(OrdemServico $ordemServico): string
    {
        $os = $ordemServico->load([
            'cliente',
            'equipamento',
            'atendente.cargo',
            'responsaveis.cargo',
            'evidencias',
        ]);

        $pdf = Pdf::loadView('pdfs.ordem-servico', compact('os'));

        $nomeArquivo = "OS-{$os->protocolo}.pdf";
        $path = "pdfs/os/{$nomeArquivo}";

        Storage::disk('local')->put($path, $pdf->output());

        return storage_path("app/{$path}");
    }

    /**
     * Gera o PDF de uma ordem de serviço e retorna como stream para download
     */
    public function streamPdf(OrdemServico $ordemServico)
    {
        $os = $ordemServico->load([
            'cliente',
            'equipamento',
            'atendente.cargo',
            'responsaveis.cargo',
            'evidencias',
        ]);

        $pdf = Pdf::loadView('pdfs.ordem-servico', compact('os'));

        return $pdf->stream("OS-{$os->protocolo}.pdf");
    }
}
