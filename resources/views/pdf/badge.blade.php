<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Crachá de Visitante</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; padding: 16px; }
        .badge { border: 2px solid #1f2937; border-radius: 8px; padding: 24px; width: 320px; }
        .header { text-align: center; margin-bottom: 16px; }
        .qr { text-align: center; margin-top: 24px; }
        .meta { font-size: 14px; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="badge">
        <div class="header">
            <h1>{{ config('app.name', 'Controle de Acesso') }}</h1>
            <p>Crachá temporário de acesso</p>
        </div>
        <div class="meta">
            <strong>Visitante:</strong> {{ $access->visitor->full_name }}<br>
            <strong>Documento:</strong> {{ $access->visitor->document_number }}<br>
            <strong>Setor:</strong> {{ $access->department->name }}<br>
            <strong>Entrada:</strong> {{ optional($access->entry_at)->format('d/m/Y H:i') }}
        </div>
        <div class="qr">
            <img src="data:image/png;base64,{{ $qrImage }}" alt="QR Code" width="200" height="200">
            <p>Apresente este QR Code na saída</p>
        </div>
    </div>
</body>
</html>
