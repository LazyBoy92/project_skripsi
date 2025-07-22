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

            <!-- Pilihan Warna -->
            <div class="mb-3">
                <label class="font-weight-bold">Pilih Warna:</label><br>
                @foreach(['Hitam', 'Merah', 'Kuning', 'Hijau'] as $warna)
                    <button class="btn btn-outline-secondary btn-sm m-1">{{ $warna }}</button>
                @endforeach
            </div>

            <!-- Pilihan Ukuran -->
            <div class="mb-3">
                <label class="font-weight-bold">Pilih Ukuran:</label><br>
                @foreach(['S', 'M', 'L'] as $ukuran)
                    <button class="btn btn-outline-secondary btn-sm m-1">{{ $ukuran }}</button>
                @endforeach
            </div>

            <!-- Kuantitas dan Tombol Beli -->
            <div class="d-flex mb-3">
                <button class="btn btn-outline-secondary">-</button>
                <input type="text" class="form-control mx-2 text-center" style="width: 60px;" value="1">
                <button class="btn btn-outline-secondary">+</button>
            </div>

            <button class="btn btn-success btn-block">Beli</button>
            <a href="{{ url('/menu_produk') }}" class="btn btn-danger btn-block mt-2">Kembali</a>
        </div>
    </div>
</div>

@endsection
