@extends('layout')

@section('content')
<div class="container mt-4">
    <h4>Bukti Pembayaran Anda</h4>

    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Order ID</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->produk->nama ?? 'Produk tidak tersedia'}}</td>
                <td>{{ $item->order_id }}</td>
                <td>
                    @if($item->status === 'success')
                        <span class="badge bg-success">Lunas</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </td>
                <td>
                    @if($item->status === 'pending')
                        <a href="{{ $item->invoice_url }}" class="btn btn-danger btn-sm">🧾 Selesaikan Pembayaran</a>
                    @else
                        <span class="text-success">✔️ Dibayar</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada pembayaran</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
