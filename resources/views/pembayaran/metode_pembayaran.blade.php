@extends('layout')

@section('title', 'Metode Pembayaran')

@section('content')

<div class="container mb-3">

    @if($success = Session::get('success'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: "info",
            title: "{{ $success }}"
        });
    </script>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Metode Pembayaran</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ url('/bayar/xendit/' . $order_id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="metode_pembayaran">Pilih metode pembayaran</label>
                            <select class="form-control mb-3" id="metode_pembayaran" name="metode_pembayaran" disabled>
                                <option value="xendit" selected>Xendit</option>
                            </select>
                            <i>* Saat ini hanya tersedia metode pembayaran dengan Xendit.</i>
                        </div>

                        <a href="{{url('/menu_produk')}}" class="btn btn-danger mt-3 me-3">Kembali</a>
                        <button type="submit" class="btn btn-success mt-3 float-right">Pilih Pembayaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
