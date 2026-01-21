<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem de Serviço Aberta</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px;">
        <h1 style="margin: 0;">Ordem de Serviço Aberta</h1>
        <p style="margin: 10px 0 0 0; font-size: 18px;"><strong>Protocolo: {{ $protocolo }}</strong></p>
    </div>

    <div style="background-color: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px;">
        <p>Olá, <strong>{{ $cliente }}</strong>!</p>
        
        <p>Sua ordem de serviço foi aberta com sucesso. Abaixo estão os detalhes:</p>
        
        <table style="width: 100%; margin: 20px 0;">
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;"><strong>Protocolo:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;">{{ $protocolo }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;"><strong>Equipamento:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;">{{ $equipamento }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;"><strong>Atendente:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;">{{ $atendente }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;"><strong>Prioridade:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd; text-transform: capitalize;">{{ $prioridade }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;"><strong>Data de Abertura:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #ddd;">{{ $dataAbertura }}</td>
            </tr>
        </table>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0;">
            <p style="margin: 0 0 10px 0;"><strong>Relato:</strong></p>
            <p style="margin: 0;">{{ $relato }}</p>
        </div>

        <p>Em breve, nossa equipe técnica irá avaliar seu equipamento e retornaremos com o diagnóstico.</p>
        
        <p>Mantenha o protocolo <strong>{{ $protocolo }}</strong> para acompanhamento.</p>
    </div>

    <div style="text-align: center; color: #888; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
        <p>Este é um e-mail automático. Por favor, não responda.</p>
        <p>&copy; {{ date('Y') }} - Sistema de Ordem de Serviço</p>
    </div>
</body>
</html>
