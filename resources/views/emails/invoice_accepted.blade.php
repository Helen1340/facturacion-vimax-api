<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Factura aceptada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f8fb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        }

        .header {
            background: #0f6efd;
            color: #fff;
            padding: 20px 24px;
        }

        .brand {
            font-size: 18px;
            font-weight: 600;
        }

        .content {
            padding: 24px;
            color: #333;
        }

        .title {
            font-size: 20px;
            margin: 0 0 12px;
        }

        .meta {
            background: #f1f5fb;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
        }

        .meta div {
            margin: 6px 0;
        }

        .cta {
            display: inline-block;
            background: #0f6efd;
            color: #fff;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .footer {
            font-size: 12px;
            color: #6b7280;
            padding: 16px 24px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="padding:24px;">
        <div class="container">
            <div class="header">
                <div class="brand">{{ $companyName }}</div>
            </div>
            <div class="content">
                <h2 class="title">Factura aceptada por la DIAN</h2>
                <p>Hola {{ $invoice->buyer->first_name ?? 'Cliente' }}, tu factura ha sido aceptada correctamente.</p>

                <div class="meta">
                    <div><strong>Número:</strong> {{ $invoice->invoice_number }}</div>
                    <div><strong>Total:</strong> {{ number_format($invoice->payable_amount, 2, '.', ',') }} {{ $invoice->document_currency_code }}</div>
                    <div><strong>CUFE:</strong> {{ $doc->cufe ?? $invoice->uuid }}</div>
                    @if($doc && $doc->validation_date)
                    <div><strong>Validación:</strong> {{ \Carbon\Carbon::parse($doc->validation_date)->format('Y-m-d H:i') }}</div>
                    @endif
                </div>

                @if($doc && $doc->qr_code)
                <p>Puedes verificar la factura en la DIAN:</p>
                <a class="cta" href="{{ $doc->qr_code }}" target="_blank" rel="noopener">Ver en DIAN</a>
                @endif

                <p style="margin-top:16px;">Adjuntamos el archivo PDF y XML de la factura para tu registro.</p>
            </div>
            <div class="footer">
                © {{ date('Y') }} {{ $companyName }} · Este mensaje fue enviado automáticamente.
            </div>
        </div>
    </div>
</body>

</html>