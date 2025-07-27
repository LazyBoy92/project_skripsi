@extends('layout')

@section('title', 'Edit Produk')

@section('content')
<div class="container mt-5">
    <h3>Edit Produk</h3>
    <form method="POST" action="{{ route('menu_produk.update', $produk->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama" value="{{ $produk->nama }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" required>{{ $produk->deskripsi }}</textarea>
        </div>

        <div class="form-group">
            <label>Harga</label>
            <input type="number" name="harga" value="{{ $produk->harga }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="tersedia" {{ $produk->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="tidak tersedia" {{ $produk->status == 'tidak tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                <option value="masih dalam pengembangan" {{ $produk->status == 'masih dalam pengembangan' ? 'selected' : '' }}>Masih Dalam Pengembangan</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nama File Gambar</label>
            <input type="text" name="gambar" value="{{ $produk->gambar }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Produk</button>
        <a href="{{ route('menu_produk.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
