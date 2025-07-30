@extends('layout')

@section('title', 'Menu Produk')

@section('content')
<div class="container">
    {{-- SweetAlert --}}
    @if($success = Session::get('success'))
        <script>
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "{{ $success }}",
                showConfirmButton: false,
                timer: 3000
            });
        </script>
    @elseif($error = Session::get('error'))
        <script>
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "error",
                title: "{{ $error }}",
                showConfirmButton: false,
                timer: 3000
            });
        </script>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="ml-4 mt-3">
                    <h5 class="text-primary">Menu Produk</h5>

                    {{-- Form Pencarian Produk --}}
                    <form action="{{ route('produk.search') }}" method="GET" class="form-inline mb-3">
                        <input type="text" name="q" class="form-control mr-2" placeholder="Cari produk..." value="{{ request('q') }}">
                             <button type="submit" class="btn btn-primary">Cari</button>
                    </form>

                    {{-- Tampilkan hasil pencarian jika ada --}}
@if(request()->has('q') && request()->q != '')
    <div class="alert alert-info">
        Menampilkan hasil pencarian untuk: <strong>{{ request()->q }}</strong>
    </div>

    @if($produk->isEmpty())
        <div class="alert alert-warning">
            Tidak ditemukan produk yang cocok dengan kata kunci <strong>{{ request()->q }}</strong>.
        </div>
    @endif
@endif




                    {{-- Tombol Tambah jika Admin --}}
                    @if (Auth::user()->role_id == 1)
                        <a href="{{ route('menu_produk.create') }}" class="btn btn-success mb-3 mt-3">
                            <i class="bi bi-plus-circle-fill"></i> Tambah
                        </a>
                    @endif
                </div>

                {{-- Daftar Produk --}}
                <div class="container my-4">
                    <div class="row">
                        @forelse($produk as $item)
                            @php
                                $id_produk = $item->id_produk ?? $item->id;
                            @endphp
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    {{-- Gambar produk --}}

                                    @php
                                        $folderPath = public_path('assets/produk_images/' . $item->gambar);
                                        $gambarPath = asset('default.png');

                                        if (is_dir($folderPath)) {
                                            $files = scandir($folderPath);
                                            foreach ($files as $file) {
                                                if (preg_match('/^' . preg_quote($item->gambar, '/') . '\.(jpg|jpeg|png)$/i', $file)) {
                                                    $gambarPath = asset('assets/produk_images/' . $item->gambar . '/' . $file);
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp


                                    <img src="{{ $gambarPath ?? asset('default.png') }}"
                                        class="card-img-top"
                                        alt="{{ $item->nama ?? $item->nama_produk }}"
                                        style="height: 250px; object-fit: cover;">


                                    {{-- Isi card --}}
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ $item->nama_produk ?? $item->nama }}</h5>
                                        <p class="card-text">{{ \Illuminate\Support\Str::limit($item->deskripsi_produk ?? $item->deskripsi, 100) }}</p>
                                        <h6 class="text-success mt-auto">Rp {{ number_format($item->harga_produk ?? $item->harga, 0, ',', '.') }}</h6>
                                    </div>

                                    {{-- Tombol --}}
                                    <div class="card-footer bg-transparent border-0">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="{{ route('produk.show', ['id_produk' => $id_produk]) }}" class="btn btn-sm btn-outline-primary">Lihat</a>

                                            @if (Auth::user()->role_id == 1)
                                                <form action="{{ route('menu_produk.destroy', $id_produk) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus {{ $item->nama_produk ?? $item->nama }} ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                                <a href="{{ route('menu_produk.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                            @endif

                                            <a href="{{ route('produk.beli', ['id' => $id_produk]) }}" class="btn btn-sm btn-outline-success">Bayar</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p class="text-muted">Produk tidak ditemukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
