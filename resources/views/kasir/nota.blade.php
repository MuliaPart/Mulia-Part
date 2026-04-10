<!DOCTYPE html>
<html>
<head>
    <title>Nota</title>
    <style>
        body {
            font-family: monospace;
            width: 300px;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .row {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <h2>Mulia Part</h2>
        <p>Jl. Pangeran Suryanegara</p>
    <h4>Ds. Pamijahan - Kec. Plumbon – Kab. Cirebon.</h4>
        <h4>Jawa Barat (45155)</h4>
    <p>Hp 081222168311 - 082219888751</p>
    </div>

    <div class="line"></div>

    <p>Invoice   : {{ $trx->invoice ?? '-' }}</p>
    <p>Tanggal   : {{ $trx->created_at }}</p>
    <p>Pelanggan : {{ $trx->customer_name }}</p>
    <p>Member    : {{ $trx->customer_type == 'member' ? 'MEMBER' : 'NON MEMBER' }}</p>

    <div class="line"></div>

    {{-- LIST ITEM --}}
    @php $grandTotal = 0; @endphp

    @foreach($trx->items as $item)

        @php
            $price = $item->sell_price ?? 0;
            $qty   = $item->qty ?? 0;
            $subtotal = $price * $qty;

            $grandTotal += $subtotal;
        @endphp

        <div>
            <strong>{{ $item->name ?? 'Produk' }}</strong>
        </div>

        <div class="row">
            <span>{{ $qty }} x {{ number_format($price) }}</span>
            <span>{{ number_format($subtotal) }}</span>
        </div>

    @endforeach

    <div class="line"></div>

    {{-- TOTAL --}}
    <div class="row">
        <strong>Total</strong>
        <strong>Rp {{ number_format($grandTotal) }}</strong>
    </div>
    <div class="line"></div>
    @if($trx->note)
        <div class="line"></div>
        <p>Keterangan:</p>
        <p>{{ $trx->note }}</p>
    @endif

    <div class="text-center" style="margin-top:10px">
        Terima kasih 🙏
    </div>

</body>
</html>