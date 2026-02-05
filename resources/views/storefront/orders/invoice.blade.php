<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .header { margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; }
        .muted { color: #6b7280; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border: 1px solid #e5e7eb; }
        th { background: #f3f4f6; text-align: left; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Invoice</div>
        <div class="muted">CosplayerWardrobe</div>
        <div class="muted">Order: {{ $order->order_number }}</div>
        <div class="muted">Tanggal: {{ $order->created_at->format('d M Y H:i') }}</div>
    </div>

    <table style="margin-bottom: 16px;">
        <tr>
            <th>Ditagihkan kepada</th>
            <th>Pengiriman</th>
        </tr>
        <tr>
            <td>
                {{ $order->user->name }}<br>
                {{ $order->user->email }}
            </td>
            <td>
                {{ $order->shipping_recipient_name }}<br>
                {{ $order->shipping_address_line }}<br>
                {{ $order->shipping_city_name }}, {{ $order->shipping_province_name }} {{ $order->shipping_postal_code }}<br>
                Telp: {{ $order->shipping_phone }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
        <tr>
            <th>Produk</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Harga</th>
            <th class="text-right">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->price_amount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->line_total_amount, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">Ongkir ({{ strtoupper($order->courier) }} {{ $order->courier_service }})</td>
            <td class="text-right">Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right"><strong>Total</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($order->grand_total_amount, 0, ',', '.') }}</strong></td>
        </tr>
        </tfoot>
    </table>

    <p class="muted" style="margin-top: 16px;">
        Terima kasih sudah berbelanja di CosplayerWardrobe.
    </p>
</body>
</html>

