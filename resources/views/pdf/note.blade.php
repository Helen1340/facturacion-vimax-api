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
            <div class="brand">{{ $note->electronicInvoice->user->company->business_name ?? 'Empresa' }}</div>
            <div class="doc-title">
                <h1>Nota {{ strtoupper($note->note_type) }} {{ $note->note_number }}</h1>
                <div>Fecha: {{ \Carbon\Carbon::parse($note->issue_date)->format('Y-m-d') }}</div>
            </div>
        </div>

        <div class="section box">
            <strong>Factura asociada</strong>
            <div>Número: {{ $note->electronicInvoice->invoice_number ?? $note->electronic_invoice_id }}</div>
        </div>

        <div class="section box">
            <strong>Detalle de la nota</strong>
            <div><b>Motivo:</b> {{ $note->reason }}</div>
            <div><b>Estado:</b> {{ $note->status }}</div>
            <div><b>Total:</b> {{ number_format($note->total_amount, 2, '.', ',') }} {{ $note->electronicInvoice->document_currency_code ?? 'COP' }}</div>
        </div>
    </div>
</body>

</html>