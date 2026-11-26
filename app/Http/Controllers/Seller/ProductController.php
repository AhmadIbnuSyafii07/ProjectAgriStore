<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('vendor_id', auth()->id())
            ->when($request->q, fn ($q, $v) => $q->where('product_name', 'like', "%{$v}%"))
            ->when($request->category, fn ($q, $v) => $q->where('category_id', $v))
            ->latest()->paginate(10)->withQueryString();

        return view('seller.products.index', [
            'products'   => $products,
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function create()
    {
        return view('seller.products.create', [
            'product'    => new Product(['is_active' => true, 'weight_gram' => 1000]),
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['vendor_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $this->own($product);

        return view('seller.products.edit', [
            'product'    => $product,
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->own($product);
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->own($product);

        // Produk yang sudah pernah dipesan tidak boleh dihapus (riwayat pesanan), cukup dinonaktifkan.
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);

            return back()->with('success', 'Produk sudah pernah dipesan, jadi hanya dinonaktifkan.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function toggle(Product $product)
    {
        $this->own($product);
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', $product->is_active ? 'Produk diaktifkan.' : 'Produk dinonaktifkan.');
    }

    private function own(Product $product): void
    {
        abort_unless($product->vendor_id === auth()->id(), 403);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:150',
            'category_id'  => 'required|exists:categories,id',
            'description'  => 'nullable|string|max:2000',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'weight_gram'  => 'required|integer|min:1',
            'image'        => 'nullable|image|max:2048',
        ]);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
