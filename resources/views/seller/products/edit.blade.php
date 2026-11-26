@extends('seller.layout')

@section('title', 'Edit Produk')

@section('content')
<div class="page-head"><div><h1>Edit Produk</h1><p>{{ $product->product_name }}</p></div></div>

<form method="POST" action="{{ route('seller.products.update', $product) }}" enctype="multipart/form-data" class="panel">
    @csrf @method('PUT')
    @include('seller.products._form')
    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Simpan perubahan</button>
        <a href="{{ route('seller.products.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>
@endsection
