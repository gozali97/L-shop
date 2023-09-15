<!DOCTYPE html>
<html>
<head>
    <title>Invoice Order</title>
    <style>
        :root {
            --primary: {{ $settings->theme['primary_color'] }};
            --secondary: {{ $settings->theme['secondary_color'] }};
            --heading: {{ $settings->theme['heading_color'] }};
            --text: {{ $settings->theme['text_color'] }};
            --primary-button: {{ $settings->theme['primary_button_color'] }};
            --secondary-button: {{ $settings->theme['secondary_button_color'] }};
        }

        .color-primary {
            color: var(--primary);
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 12px;
        }

        @page {
            size: A4;
            margin: 0;
        }

        .invoice {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            padding: 10px;
        }
        .invoice-grid {
            display: grid;
            gap: 10px;
        }

        .item{
            grid-column: 2;
            grid-row: 1;
        }

        .invoice-header h2 {
            margin: 0;
        }

        .invoice-store {
            margin-bottom: 20px;
        }

        .invoice-store table {
            width: 50%;
            border-collapse: collapse;
        }

        .invoice-store table td {
            padding: 5px;
            margin-left: 10px;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-details table {
            width: 50%;
            border-collapse: collapse;
        }

        .invoice-details table td {
            padding: 10px;
        }

        .invoice-items {
            margin-bottom: 20px;
        }

        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-items table th,
        .invoice-items table td {
            padding: 10px;
        }

        .invoice-items table th {
            background-color: #f4f4f4;
        }

        .invoice-total {
            text-align: right;
        }

        .invoice-total table {
            width: 50%;
            margin-left: auto;
        }

        .invoice-total table td {
            padding: 10px;
        }

        .text-right{
            text-align: right;
        }

        .-m-4{
            margin-top: -1em;
        }

        .font-bold{
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="invoice">
    <table width="100%">
        <tr>
            <td width="50%">
                <img src="{{Helper::getBrandLogoUrl()}}" width="30px">
            </td>
            <td width="50%">
                <h2 class="text-right">INVOICE</h2>
                <p class="text-right -m-4 color-primary">{{$order->order_number}}</P>
            </td>
        </tr>
    </table>
    <table width="100%" style="margin-bottom: 2em">
        <tr>
            <td width="45%">
                <table>
                    <tr>
                        <td colspan="2">DITERBITKAN ATAS NAMA</td>
                    </tr>
                    <tr>
                        <td width="20%">Penjual </td>
                        <td>: {{Helper::getSetting()->brand_name}}</td>
                    </tr>
                </table>

            </td>
            <td width=55%">
                <table>
                    <tr>
                        <td class="font-bold">Untuk</td>
                    </tr>
                    <tr>
                        <td width="45%">Pembeli</td>
                        <td width="5%">:</td>
                        <td width="50%">{{ucwords($order->first_name)}} {{ucwords($order->last_name)}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pembelian</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Alamat Pengiriman</td>
                        <td>:</td>
                        <td>{{ucwords($order->first_name)}} {{ucwords($order->last_name)}}, {{$order->phone}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ucwords($order->address1)}} - {{$order->post_code}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div class="invoice-items">
        <table>
            <thead>
            <tr style="border-top: 2px solid gray;border-bottom: 2px solid gray">
                <th>Info Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
            </tr>
            </thead>
            <tbody>
            @php
                $quantity = 0;
                $sumWeight = 0;
            @endphp
            @foreach($product as $p)
            <tr style="border-bottom: 1px solid gray">
                <td>{{$p->title}}</td>
                <td style="text-align: center">{{$p->quantity}}</td>
                <td style="text-align: center">Rp. {{number_format($p->price)}}</td>
                @php
                    $subproduct = 0;
                    $subproduct = $p->price * $p->quantity;
                    $weight = 0;
                    $weight = $p->quantity * $p->weight;
                @endphp
                <td style="text-align: center">Rp. {{number_format($subproduct)}}</td>
            </tr>
                @php
                    $quantity = $p->quantity++;
                    $sumWeight = $weight++;
                @endphp
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="invoice-total">
        <table style="border-bottom: 1px solid gray">
            <tr class="font-bold">
                <td style="text-align: left">Total Harga Barang ({{$quantity}} Barang)</td>
                <td style="text-align: right">Rp. {{number_format($subproduct)}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Total Ongkos Kirim ({{$sumWeight}} gram)</td>
                <td style="text-align: right">Rp. {{number_format($order->price_shipping)}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Total Coupon Barang</td>
                <td style="text-align: right">Rp. {{number_format($order->price_coupon)}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Biaya Asuransi Pengiriman</td>
                <td style="text-align: right">Rp. 0</td>
            </tr>
        </table>
        <table style="border-bottom: 1px solid gray">
            <tr class="font-bold">
                @php
                $total = 0;
                $total = $subproduct + $order->price_shipping + $order->price_coupon;
                @endphp
                <td style="text-align: left">Total Belanja</td>
                <td style="text-align: right">Rp. {{number_format($total)}}</td>
            </tr>
        </table>
    </div>
    <table width="100%" style="margin-top: 2em; border-top: 1px solid gainsboro; font-size: 12px">
        <tr>
            <td width="50%">
                <p>Kurir</p>
                <p>{{ucwords(str_replace('-', ' ', $order->type))}} - {{$order->service}}</p>
            </td>
            <td width="50%">
                <p>Metode Pembayaran</p>
                @php
                    $metode = 'Bank Transfer';
                    if($order->payment_method == 'va'){
                        $metode = 'Virtual Account';
                    }
                @endphp
                <p>{{ucwords(str_replace('-', ' ', $order->payment_method))}}</p>
                <p>{{$order->bank_name}} - {{$order->account_number}}</p>
            </td>
        </tr>
    </table>
    <table width="100%" class="invoice-grid" style="margin-top: 4em; font-size: 10px">
        <tr>
            <td width="50%">
                    <p>Invoice ini sah dan diproses oleh komputer</p>
                    <p>Silahkan hubungi <span class="color-primary">{{Helper::getSetting()->brand_name}}</span> apabila kamu membutuhkan bantuan</p>
            </td>
            <td width="50%">
                    <p style="margin-top: 3.1em">Terakhir diupdate {{date('d F Y H:m')}} WIB</p>
            </td>
        </tr>
    </table>

    </div>
<script defer>
    window.print();
</script>
</body>
</html>
