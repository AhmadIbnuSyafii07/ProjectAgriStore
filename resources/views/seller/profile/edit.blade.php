@extends('seller.layout')

@section('title', 'Profil Toko')

@section('content')
<div class="page-head"><div><h1>Profil Toko</h1><p>Ubah nama toko dan akun login Anda.</p></div></div>

<form method="POST" action="{{ route('seller.profile.update') }}" class="panel">
    @csrf @method('PUT')
    <div class="form-grid">
        <div class="field">
            <label for="name">Nama toko</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="field">
            <label for="password">Password baru</label>
            <input type="password" id="password" name="password" autocomplete="new-password">
            <small>Kosongkan jika tidak ingin mengganti password.</small>
        </div>
        <div class="field">
            <label for="password_confirmation">Ulangi password baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
        </div>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit">Simpan perubahan</button></div>
</form>
@endsection
