{{-- Blade template for generating PDF of the item list for an order --}}
    <!DOCTYPE html>
<html>
<head>
    <title>
        Order @if($order)
            - {{$order->order_number}}
        @endif
    </title>
    <style>
        * {
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        }

        .container {
            margin: 0 15px;
        }

        .d-flex {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            height: fit-content;
        }

        .text-end {
            text-align: end;
        }

        .text-center {
            text-align: center;
        }

        .item-list {
            font-size: 24px;
        }

        p {
            margin: 0;
        }

        .package-of {
            font-size: 36px
        }
    </style>
</head>
<body>
<main class="container">
    {{-- HEADER --}}
    <div class="d-flex">
        <div>
            <p class="item-list">ITEM LIST</p>
            <p>{{ $order->shipping?->type }}</p>
            <p>{{ $order->shipping?->service }}</p>
        </div>
        <div class="text-end">
            <p>PACKAGE</p>
            <p class="package-of">1 OF 1</p>
        </div>
    </div>

    <hr>

    {{-- ORDER DETAIL --}}
    <div class="d-flex">
        <p>ORDER NUMBER</p>
        <p>{{ $order->order_number }}</p>
    </div>

    <hr>

    <div class="d-flex">
        <p>CREATED TIME</p>
        <p>{{ $order->created_at }}</p>
    </div>

    <hr>

    <p>BARCODE ORDER</p>
    <div class="barcode-order text-center">
        <svg id="barcode"></svg>
    </div>
    <div
        class="text-center w-100">{{ $order->order_number }} {{ $weight > 1000 ? ($weight/1000) . 'Kg' : $weight . 'G' }}</div>

    <hr>

    {{-- ITEM LIST --}}
    <div class="text-center w-100">===== ITEMS =====</div>
    @foreach($items as $item)
        <div class="d-flex">
            <div>
                <p>{{ $item->product->sku ?? '-' }}</p>
                <p>{{ $item->product->title }}</p>
            </div>
            <p>{{ $item->quantity }} PC</p>
        </div>
    @endforeach
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"
        integrity="sha512-QEAheCz+x/VkKtxeGoDq6nsGyzTx/0LMINTgQjqZ0h3+NjP+bCsPYz3hn0HnBkGmkIFSr7QcEZT+KyEM7lbLPQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script defer>
    JsBarcode("#barcode", "{{ route('order.show', $order->id) }}", {
        width: 1,
        displayValue: false,
    });
    window.print();
</script>
</body>
</html>
