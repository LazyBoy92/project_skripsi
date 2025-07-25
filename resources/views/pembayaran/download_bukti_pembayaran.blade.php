<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Download Bukti Pembayaran</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

    <div class="container">

        <div class="row">
            <div class="col">
                <hr class="border" />
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h4 class="text-center"></h4>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <hr class="border" />
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <h2 class="mb-3" style="color: gray;">Toko Busana Darisem</h2>

                @foreach($produk as $itemProduk)
                @php
                    $produkBeliItem = $itemProduk->produk_beli->firstWhere('order_id', $pembayaran->order_id);
                @endphp

                @if($produkBeliItem)
                <p>Nama : {{ $user->name }}</p>
                <p><strong>Tanggal Transaksi:</strong> {{ \Carbon\Carbon::parse($pembayaran->paid_at)->format('d F Y') }}</p>
                <p><strong>Penjual:</strong> Darisem (083846449309)</p>

                @endif
                @endforeach
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <table class="table table-bordered">
                    <thead class="table-active">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Produk</th>
                            <th>Deskripsi</th>
                            <th>Qty</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php $no = 1; @endphp
@foreach($produk as $itemProduk)
    @php
        $produkBeliItem = $itemProduk->produk_beli->firstWhere('order_id', $pembayaran->order_id);
    @endphp

    @if($produkBeliItem)
    <tr class="text-center">
        <td>{{ $no++ }}</td>
        <td>{{ $itemProduk->nama }}</td>
        <td>{{ $itemProduk->deskripsi }}</td>
        <td>{{ $produkBeliItem->qty }}</td>
        <td>{{ number_format($pembayaran->total, 0, ',', '.') }}</td>
    </tr>
    @endif
@endforeach

                    </tbody>
                </table>
            </div>
        </div>

        <hr class="border" />

        <div class="row mb-3">
            <div class="col">
                <p class="text-center"> Terima kasih telah membeli produk di Tokoku.
                </p>
            </div>
        </div>

        <hr class="border" />

        <div class="row">
            <div class="col">
                <i> Catatan : Harap kirim bukti ke Admin jika ada kendala sesuatu.</i>
            </div>
        </div>
    </div>

</body>

</html>