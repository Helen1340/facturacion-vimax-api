<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .wrap {
            width: 100%;
            padding: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .brand {
            font-size: 18px;
            font-weight: 700;
            color: #0f6efd;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h1 {
            font-size: 18px;
            margin: 0;
        }

        .section {
            margin-top: 14px;
        }

        .box {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f7f7f9;
            font-weight: 600;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="header">
            <div class="brand">
                @if(!empty($invoice->user->company->logo_url))
                    <img src="{{ $invoice->user->company->logo_url }}" alt="Logo" style="max-height:48px;">
                @else
                    {{ $invoice->user->company->business_name ?? 'Empresa' }}
                @endif
            </div>
            <div class="doc-title">
                <h1>Factura {{ $invoice->invoice_number ?? $invoice->id }}</h1>
                <div>Fecha: {{ \Carbon\Carbon::parse($invoice->issue_date)->format('Y-m-d') }}</div>
                @if(!empty($invoice->uuid))
                    <div>CUFE: {{ $invoice->uuid }}</div>
                @endif
            </div>
        </div>

        <div class="section box">
            <table>
                <tr>
                    <th>Vendedor</th>
                    <th>Cliente</th>
                </tr>
                <tr>
                    <td>
                        {{ $invoice->user->company->business_name ?? 'Empresa' }}<br>
                        {{ $invoice->user->email ?? '' }}<br>
                        {{ $invoice->user->company->address ?? '' }}
                    </td>
                    <td>
                        {{ $invoice->buyer->first_name ?? 'Cliente' }}<br>
                        {{ $invoice->buyer->email ?? '' }}<br>
                        {{ $invoice->buyer->address ?? '' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="section box">
            <table>
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th class="right">Cantidad</th>
                        <th class="right">Precio</th>
                        <th class="right">Descuento</th>
                        <th class="right">Impuestos</th>
                        <th class="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($invoice->invoiceDetails ?? []) as $d)
                    <tr>
                        <td>{{ $d->description ?? ($d->item->name ?? '') }}</td>
                        <td class="right">{{ number_format($d->quantity ?? 0, 0) }}</td>
                        <td class="right">{{ number_format($d->unit_price ?? 0, 2, '.', ',') }}</td>
                        <td class="right">{{ number_format($d->discount_amount ?? 0, 2, '.', ',') }}</td>
                        <td class="right">{{ number_format($d->tax_amount ?? 0, 2, '.', ',') }}</td>
                        <td class="right">{{ number_format($d->total_line_amount ?? 0, 2, '.', ',') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section box">
            <table>
                <tr>
                    <td class="right"><strong>Subtotal</strong></td>
                    <td class="right">{{ number_format($invoice->line_extension_amount ?? 0, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>Impuestos</strong></td>
                    <td class="right">{{ number_format(($invoice->tax_inclusive_amount ?? 0) - ($invoice->tax_exclusive_amount ?? 0), 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>Total</strong></td>
                    <td class="right">{{ number_format($invoice->payable_amount ?? 0, 2, '.', ',') }} {{ $invoice->document_currency_code ?? 'COP' }}</td>
                </tr>
            </table>
        </div>

        <div class="section box">
            <strong>Pago</strong>
            <div><b>Método:</b> {{ $invoice->payment_means_name ?? 'Contado' }} ({{ $invoice->payment_means_code ?? '10' }})</div>
        </div>

        @if(isset($invoice->electronicDocuments) && $invoice->electronicDocuments->count())
            @php $doc = $invoice->electronicDocuments->last(); @endphp
            @if(!empty($doc->qr_code))
                <div class="section box">
                    <strong>QR de validación DIAN</strong><br>
                    <img src="{{ $doc->qr_code }}" alt="QR" style="max-height:120px;">
                </div>
            @endif
        @endif
    </div>
</body>

</html>