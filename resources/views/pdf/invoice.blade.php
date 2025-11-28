<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Factura {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            margin-right: 20px;
        }

        .qr img, .qr svg {
            width: 120px;
            height: 120px;
        }

        .company-details {
            flex: 1;
        }

        .company-info {
            margin-bottom: 20px;
        }

        .invoice-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .totals {
            float: right;
            width: 300px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
        }

        .watermark {
            opacity: 0.1;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 72px;
            color: #ccc;
        }
    </style>
</head>

<body>
    <!-- Marca de agua opcional 
    <div class="watermark">APROBADA DIAN</div>-->

    <div class="header">
        @if(($enableImages ?? true) && optional(optional($invoice->user)->company)->logo_url)
        <div class="logo">
            <img src="{{ optional(optional($invoice->user)->company)->logo_url }}" alt="Logo" style="max-width: 100px; max-height: 80px;">
        </div>
        @endif

        <div class="company-details">
            <h1>FACTURA ELECTRÓNICA</h1>
            <h2>{{ optional(optional($invoice->user)->company)->business_name ?? 'Empresa' }}</h2>
            <p>NIT: {{ optional(optional($invoice->user)->company)->nit ?? 'Sin NIT' }}</p>
            @if(optional(optional($invoice->user)->company)->trade_name)
            <p>Nombre Comercial: {{ optional(optional($invoice->user)->company)->trade_name }}</p>
            @endif
        </div>

        @if(($qrSvg ?? null) || (($enableImages ?? true) && ($qrImageUrl ?? null)))
        <div class="qr">
            @if(($qrSvg ?? null))
                {!! $qrSvg !!}
            @elseif(($enableImages ?? true) && ($qrImageUrl ?? null))
                <img src="{{ $qrImageUrl }}" alt="QR">
            @endif
        </div>
        @endif
    </div>

    <div class="company-info">
        <strong>EMISOR:</strong><br>
        {{ optional(optional($invoice->user)->company)->business_name ?? '' }}<br>
        @if(optional(optional($invoice->user)->company)->address && optional(optional($invoice->user)->company)->address !== 'Sin definir')
        {{ optional(optional($invoice->user)->company)->address }}<br>
        @endif
        @if(optional(optional($invoice->user)->company)->city && optional(optional($invoice->user)->company)->city !== 'Sin definir')
        {{ optional(optional($invoice->user)->company)->city }},
        @endif
        @if(optional(optional($invoice->user)->company)->department && optional(optional($invoice->user)->company)->department !== 'Sin definir')
        {{ optional(optional($invoice->user)->company)->department }}<br>
        @endif
        @if(optional(optional($invoice->user)->company)->phone && optional(optional($invoice->user)->company)->phone !== 'Sin definir')
        Tel: {{ optional(optional($invoice->user)->company)->phone }}<br>
        @endif
        @if(optional(optional($invoice->user)->company)->email && optional(optional($invoice->user)->company)->email !== 'Sin definir')
        Email: {{ optional(optional($invoice->user)->company)->email }}
        @endif
    </div>

    <div class="invoice-info">
        <strong>INFORMACIÓN DE LA FACTURA</strong><br>
        <strong>No:</strong> {{ $invoice->invoice_number }}<br>
        <strong>Fecha:</strong> {{ $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') : '' }}<br>
        <strong>CUFE:</strong> {{ $invoice->uuid ?? 'Pendiente' }}<br>

        <br><strong>INFORMACIÓN DEL CLIENTE</strong><br>
        <strong>Nombre:</strong> {{ optional($invoice->buyer)->first_name ?? '' }}<br>
        <strong>Documento:</strong> {{ optional($invoice->buyer)->document_type ?? '' }} {{ optional($invoice->buyer)->document_number ?? '' }}<br>
        @if(optional($invoice->buyer)->address)
        <strong>Dirección:</strong> {{ optional($invoice->buyer)->address }}<br>
        @endif
        @if(optional($invoice->buyer)->email)
        <strong>Email:</strong> {{ optional($invoice->buyer)->email }}<br>
        @endif
        @if(optional($invoice->buyer)->phone)
        <strong>Teléfono:</strong> {{ optional($invoice->buyer)->phone }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Descuento</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->invoiceDetails as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->description }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>${{ number_format($detail->unit_price, 2) }}</td>
                <td>${{ number_format($detail->discount_amount, 2) }}</td>
                <td>${{ number_format($detail->total_line_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td>${{ number_format($invoice->tax_exclusive_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Impuestos:</strong></td>
                <td>${{ number_format(($invoice->tax_inclusive_amount ?? 0) - ($invoice->tax_exclusive_amount ?? 0), 2) }}</td>
            </tr>
            @if(($invoice->total_discount ?? 0) > 0)
            <tr>
                <td><strong>Descuentos:</strong></td>
                <td>${{ number_format($invoice->total_discount ?? 0, 2) }}</td>
            </tr>
            @endif
            <tr style="background-color: #f8f9fa;">
                <td><strong>TOTAL A PAGAR:</strong></td>
                <td><strong>${{ number_format($invoice->payable_amount ?? 0, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($invoice->observation)
    <div style="margin-top: 20px;">
        <strong>Observaciones:</strong><br>
        {{ $invoice->observation }}
    </div>
    @endif

    @if($invoice->uuid)
    <div class="footer">
        <p><strong>Factura electrónica generada automáticamente</strong></p>
        <p><strong>CUFE:</strong> {{ $invoice->uuid }}</p>
        <p>Este documento es válido como factura de venta según normativa DIAN</p>
        @if($invoice->sent_at)
        <p>Fecha de validación: {{ \Carbon\Carbon::parse($invoice->sent_at)->format('d/m/Y H:i:s') }}</p>
        @endif
    </div>
    @endif
</body>

</html>
