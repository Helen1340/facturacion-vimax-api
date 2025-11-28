<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Factura Aprobada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background: white;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }

        .factura-info {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #4CAF50;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1> Factura Aprobada </h1>
        </div>

        <div class="content">
            <p>Hola <strong>{{ $cliente->first_name }}</strong>,</p>

            <p>Te informamos que tu factura ha sido <strong>aprobada exitosamente</strong> por la DIAN.</p>

            <div class="attachment">
                <h3> Factura en PDF Adjunta</h3>
                <p>Hemos adjuntado la factura completa en formato PDF que incluye:</p>
                <ul>
                    <li>Información completa del cliente</li>
                    <li>Detalles de todos los items</li>
                    <li>Totales y impuestos</li>
                    <li>CUFE de validación DIAN</li>
                </ul>
                <p><strong>Archivo:</strong> factura-{{ $factura->invoice_number }}.pdf</p>
            </div>

            <p>Puedes descargar tu factura en PDF o XML desde nuestro sistema cuando lo necesites.</p>

            <p>Gracias por tu preferencia.</p>

            <p><strong>Atentamente,</strong><br>
                {{ $factura->user->company->business_name ?? 'Sistema de Facturación' }}
            </p>
        </div>

        <div class="footer">
            <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
            <p>&copy; {{ date('Y') }} {{ $factura->user->company->business_name ?? 'Sistema de Facturación' }}</p>
        </div>
    </div>
</body>

</html>