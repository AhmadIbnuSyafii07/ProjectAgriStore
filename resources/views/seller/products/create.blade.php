@extends('seller.layout')

@section('title', 'Tambah Produk')

@section('content')
<div class="page-head"><div><h1>Tambah Produk</h1><p>Isi data produk yang akan dijual.</p></div></div>

<form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="panel">
    @csrf
    @include('seller.products._form')
    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Simpan produk</button>
        <a href="{{ route('seller.products.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>
@endsection
