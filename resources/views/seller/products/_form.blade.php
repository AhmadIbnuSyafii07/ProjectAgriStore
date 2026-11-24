<div class="form-grid">
    <div class="field full">
        <label for="product_name">Nama produk</label>
        <input type="text" id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" required>
    </div>

    <div class="field">
        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->category_name }}</option>
            @endforeach
        </select>
        @if ($categories->isEmpty())
            <small>Belum ada kategori. Kategori dikelola Super Admin (atau jalankan SellerDemoSeeder).</small>
        @endif
    </div>

    <div class="field">
        <label for="price">Harga per unit (Rp)</label>
        <input type="number" id="price" name="price" min="0" step="1" value="{{ old('price', $product->price !== null ? (int) $product->price : '') }}" required>
    </div>

    <div class="field">
        <label for="stock">Stok</label>
        <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}" required>
    </div>

    <div class="field">
        <label for="weight_gram">Berat (gram)</label>
        <input type="number" id="weight_gram" name="weight_gram" min="1" value="{{ old('weight_gram', $product->weight_gram) }}" required>
        <small>Dipakai untuk menghitung ongkir. 1 kg = 1000 g.</small>
    </div>

    <div class="field full">
        <label for="description">Deskripsi</label>
        <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="field full">
        <label for="image">Foto produk</label>
        @if ($product->image_url)
            <img class="preview" src="{{ $product->image_url }}" alt="Foto saat ini">
            <small>Pilih file baru hanya jika ingin mengganti foto.</small>
        @endif
        <input type="file" id="image" name="image" accept="image/*">
        <small>JPG/PNG, maksimal 2 MB.</small>
    </div>

    <div class="field full">
        <label class="check">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
            Tampilkan di katalog (aktif)
        </label>
    </div>
</div>
