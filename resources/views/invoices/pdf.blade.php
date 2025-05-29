<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #3a86ff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-family: Arial, sans-serif;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .back-btn:hover {
            background-color: #2563eb;
        }
        @media print {
            .back-btn {
                display: none;
            }
        }
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-info table {
            width: 100%;
        }
        .invoice-info td {
            padding: 5px;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .products-table th, .products-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .products-table th {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
        }
        .totals {
            float: right;
            width: 300px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 5px;
        }
    </style>
</head>
<body>
    <a href="{{ route('invoices.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Volver al listado
    </a>
    <div class="header">
        <h2>FACTURA ELECTRÓNICA</h2>
        <p>{{ config('app.name') }}</p>
        <p>Documento Tributario Electrónico</p>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td><strong>No. Factura:</strong> {{ $invoice->invoice_number }}</td>
                <td><strong>Fecha:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong> {{ $invoice->customer_name }}</td>
                <td><strong>NIT:</strong> {{ $invoice->customer_nit ?? 'C/F' }}</td>
            </tr>
            <tr>
                <td><strong>Dirección:</strong> {{ $invoice->customer_address ?? 'N/A' }}</td>
                <td><strong>Teléfono:</strong> {{ $invoice->customer_phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Método de Pago:</strong> 
                    @switch($invoice->payment_method)
                        @case('cash')
                            Efectivo
                            @break
                        @case('card')
                            Tarjeta
                            @break
                        @case('transfer')
                            Transferencia
                            @break
                    @endswitch
                </td>
                <td><strong>Email:</strong> {{ $invoice->customer_email ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $detail)
            <tr>
                <td>{{ $detail->product->code }}</td>
                <td>{{ $detail->product->name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>Q{{ number_format($detail->price, 2) }}</td>
                <td>Q{{ number_format($detail->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Total:</strong></td>
                <td>Q{{ number_format($invoice->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Este documento es una representación gráfica de un Documento Tributario Electrónico (DTE)</p>
        @if(!config('app.production'))
        <p><strong>DOCUMENTO DE PRUEBA - NO VÁLIDO PARA EFECTOS FISCALES</strong></p>
        @endif
    </div>
</body>
</html>
