<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem de Serviço Concluída</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
            border-radius: 3px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .info-box strong {
            color: #28a745;
        }
        .highlight {
            background-color: #d4edda;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .highlight p {
            margin: 5px 0;
            font-size: 18px;
        }
        .highlight .valor {
            font-size: 24px;
            font-weight: bold;
            color: #155724;
        }
        .action-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .action-box h3 {
            margin-top: 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .attachment-notice {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .attachment-notice p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Serviço Concluído!</h1>
    </div>

    <div class="content">
        <p>Olá, <strong>{{ $cliente }}</strong>!</p>

        <p>Temos o prazer de informar que a ordem de serviço <strong>{{ $protocolo }}</strong> foi concluída com sucesso!</p>

        <div class="info-box">
            <p><strong>Equipamento:</strong> {{ $equipamento }}</p>
            <p><strong>Data de Conclusão:</strong> {{ $dataConclusao }}</p>
        </div>

        <div class="highlight">
            <p>Valor Total do Serviço</p>
            <p class="valor">{{ $valorTotal }}</p>
        </div>

        <div class="action-box">
            <h3>📦 Seu equipamento está pronto para retirada!</h3>
            <p>Por favor, compareça à nossa assistência técnica para retirar seu equipamento.</p>
            <p><strong>Importante:</strong> Traga um documento com foto para identificação.</p>
        </div>

        <div class="attachment-notice">
            <p><strong>📄 Laudo Técnico Anexo</strong></p>
            <p>Em anexo você encontrará o laudo técnico completo com todos os detalhes do serviço realizado.</p>
        </div>

        <p>Caso tenha alguma dúvida, não hesite em entrar em contato conosco.</p>

        <p>Agradecemos pela confiança!</p>
    </div>

    <div class="footer">
        <p>Este é um e-mail automático. Por favor, não responda.</p>
        <p>© {{ date('Y') }} - Assistência Técnica</p>
    </div>
</body>
</html>
