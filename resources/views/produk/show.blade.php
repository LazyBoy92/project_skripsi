@extends('layout')

@section('title', 'Detail Produk')

@section('content')

<div class="container mt-4">
    <div class="row">
        <!-- Gambar Produk -->
        <div class="col-md-5 text-center">
            <img src="{{ asset('assets/produk_images/' . $produk->gambar . '/'. $produk->gambar . ".jpg") }}" alt="{{ $produk->nama }}"
                 class="img-fluid rounded" style="max-height: 400px;">
        </div>

        <!-- Informasi Produk -->
        <div class="col-md-7">
            <h4>{{ $produk->nama }}</h4>
            <small class="text-muted">Terjual 20</small>
            <h5 class="text-success mt-2">Rp {{ number_format($produk->harga, 0, ',', '.') }}</h5>
            <p class="text-secondary"><del>Rp 75.000</del></p>

            <p class="mt-3">{{ $produk->deskripsi }}</p>


         

            <!-- Kuantitas dan Tombol Beli -->
            <form action="{{ route('produk.beli', $produk->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="font-weight-bold">Jumlah:</label><br>
                    <input type="number" name="qty" value="1" min="1" class="form-control w-25" required>
                </div>
                <button type="submit" class="btn btn-success btn-block">Bayar</button>
            </form>


            <script>
                 function changeQty(amount) {
                    let input = document.getElementById('qtyInput');
                    let value = parseInt(input.value) || 1;
                     value = Math.max(1, value + amount); // qty minimal 1
                    input.value = value;
                }
            </script>


            <a href="{{ url('/menu_produk') }}" class="btn btn-danger btn-block mt-2">Kembali</a>
        </div>
    </div>
</div>

@endsection
