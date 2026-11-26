<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('vendor_id', auth()->id())
            ->when($request->q, fn ($q, $v) => $q->where('product_name', 'like', "%{$v}%"))
            ->when($request->low, fn ($q) => $q->where('stock', '<=', Product::LOW_STOCK))
            ->orderBy('stock')->paginate(12)->withQueryString();

        return view('seller.stock.index', compact('products'));
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === auth()->id(), 403);
        $data = $request->validate(['stock' => 'required|integer|min:0']);

        $product->update($data);

        return back()->with('success', "Stok {$product->product_name} diperbarui.");
    }
}
