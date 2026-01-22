<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ordem de Serviço {{ $os->protocolo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #333;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            width: 30%;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        .text-block {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
            white-space: pre-wrap;
        }
        .evidencias-list {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }
        .evidencias-list li {
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        .tecnicos-list {
            margin: 5px 0;
            padding-left: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-concluida {
            background-color: #d4edda;
            color: #155724;
        }
        .status-execucao {
            background-color: #fff3cd;
            color: #856404;
        }
        .valor-total {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ORDEM DE SERVIÇO</h1>
        <p>Protocolo: <strong>{{ $os->protocolo }}</strong></p>
    </div>

    <div class="section">
        <div class="section-title">INFORMAÇÕES GERAIS</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Data de Abertura:</div>
                <div class="info-value">{{ $os->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($os->data_conclusao)
            <div class="info-row">
                <div class="info-label">Data de Conclusão:</div>
                <div class="info-value">{{ $os->data_conclusao->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $os->status }}">
                        {{ strtoupper($os->status) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Prioridade:</div>
                <div class="info-value">{{ strtoupper($os->prioridade) }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">DADOS DO CLIENTE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">{{ $os->cliente->nome }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">CPF/CNPJ:</div>
                <div class="info-value">{{ $os->cliente->cpf_cnpj }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $os->cliente->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">WhatsApp:</div>
                <div class="info-value">{{ $os->cliente->whatsapp }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">EQUIPAMENTO</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Tipo:</div>
                <div class="info-value">{{ $os->equipamento->tipo }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Marca:</div>
                <div class="info-value">{{ $os->equipamento->marca }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Modelo:</div>
                <div class="info-value">{{ $os->equipamento->modelo }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">ATENDIMENTO</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Atendente:</div>
                <div class="info-value">{{ $os->atendente->nome }} ({{ $os->atendente->cargo->nome }})</div>
            </div>
            @if($os->responsaveis->count() > 0)
            <div class="info-row">
                <div class="info-label">Técnicos Responsáveis:</div>
                <div class="info-value">
                    <ul class="tecnicos-list">
                        @foreach($os->responsaveis as $tecnico)
                            <li>{{ $tecnico->nome }} ({{ $tecnico->cargo->nome }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">RELATO DO CLIENTE</div>
        <div class="text-block">{{ $os->relato_cliente }}</div>
    </div>

    @if($os->diagnostico_tecnico)
    <div class="section">
        <div class="section-title">DIAGNÓSTICO TÉCNICO</div>
        <div class="text-block">{{ $os->diagnostico_tecnico }}</div>
    </div>
    @endif

    @if($os->evidencias->count() > 0)
    <div class="section">
        <div class="section-title">EVIDÊNCIAS REGISTRADAS</div>
        <ul class="evidencias-list">
            @foreach($os->evidencias as $index => $evidencia)
                <li>
                    <strong>{{ $index + 1 }}.</strong>
                    {{ $evidencia->legenda ?? 'Sem descrição' }}
                    ({{ $evidencia->momento }})
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="section">
        <div class="section-title">VALOR DO SERVIÇO</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Valor Total:</div>
                <div class="info-value">
                    <span class="valor-total">R$ {{ number_format($os->valor_total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i') }}</p>
        <p>Este documento é uma representação digital da Ordem de Serviço {{ $os->protocolo }}</p>
    </div>
</body>
</html>
