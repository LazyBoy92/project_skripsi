@extends('layout')

@section('title', 'Profile')

@section('content')

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
    icon: "success",
    title: "{{ $success }}"
    });
</script>

@elseif($error = Session::get('error'))
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
    icon: "error",
    title: "{{ $error }}"
    });
</script>

@elseif($warning = Session::get('warning'))
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
    icon: "warning",
    title: "{{ $warning }}"
    });
</script>
@endif

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Profile Anda</h4>
                </div>

                <div class="card-body">
                <form method="post" action="{{url('update_profile')}}">
    @csrf

    <div class="input-group mb-4">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1"><i class="bi bi-person-fill"></i></span>
        </div>
        <input type="text" class="form-control" name="name" autocomplete="off"
            placeholder="Nama Anda" value="{{ old('name', $user->name) }}">
    </div>

    @if($user->name == '')
        <p><i>* Isi nama lengkap Anda (Wajib).</i></p>
    @endif

    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon2"><i class="bi bi-envelope-at-fill"></i></span>
        </div>
        <input type="email" class="form-control" placeholder="Email" name="email"
            value="{{ $user->email }}" readonly>
    </div>

    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon3"><i class="bi bi-telephone-fill"></i></span>
        </div>
        <input type="number" class="form-control" name="nomor_telepon"
            placeholder="Nomor telepon" autocomplete="off"
            value="{{ old('nomor_telepon', $customer->nomor_telepon ?? '') }}">
    </div>

    <div class="input-group mb-3">
    <div class="input-group-prepend">
        <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
    </div>
    <textarea name="alamat_pengiriman" id="alamat_pengiriman"
        class="form-control" rows="3"
        placeholder="Alamat Pengiriman">{{ old('alamat_pengiriman', $customer->alamat_pengiriman ?? '') }}</textarea>
</div>



    @if(empty($customer->nomor_telepon))
        <p><i>* Isi nomor telepon agar admin bisa memudahkan menghubungi Anda jika ada kendala pembayaran (Wajib).</i></p>
    @endif

    <button type="submit" name="simpan" class="btn btn-success mt-3 float-right">Simpan</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection