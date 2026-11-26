<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['buyer', 'items.product'])
            ->where('vendor_id', auth()->id())
            ->when($request->status === 'new', fn ($q) => $q->whereIn('order_status', ['pending_payment', 'processing']))
            ->when(in_array($request->status, ['shipped', 'completed', 'cancelled']), fn ($q) => $q->where('order_status', $request->status))
            ->when($request->q, function ($q, $v) {
                $q->where(function ($w) use ($v) {
                    $w->where('order_number', 'like', "%{$v}%")
                      ->orWhereHas('buyer', fn ($b) => $b->where('name', 'like', "%{$v}%"));
                });
            })
            ->latest()->paginate(10)->withQueryString();

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->own($order);
        $order->load(['buyer', 'items.product', 'payment']);

        return view('seller.orders.show', compact('order'));
    }

    /** Verifikasi bukti pembayaran: action = valid | reject */
    public function payment(Request $request, Order $order)
    {
        $this->own($order);
        $request->validate(['action' => 'required|in:valid,reject']);

        if ($order->payment_status !== 'waiting') {
            return back()->with('error', 'Pembayaran pesanan ini tidak sedang menunggu verifikasi.');
        }

        if ($request->action === 'valid') {
            $order->update(['payment_status' => 'paid', 'order_status' => 'processing']);
            $order->payment?->update(['payment_date' => now()]);

            return back()->with('success', 'Pembayaran dinyatakan Valid. Pesanan siap diproses.');
        }

        $order->update(['payment_status' => 'rejected']);

        return back()->with('success', 'Pembayaran ditolak.');
    }

    /** Input nomor resi -> status pesanan menjadi Dikirim */
    public function ship(Request $request, Order $order)
    {
        $this->own($order);
        $data = $request->validate(['tracking_number' => 'required|string|max:60']);

        if ($order->payment_status !== 'paid' || $order->order_status !== 'processing') {
            return back()->with('error', 'Pesanan hanya bisa dikirim setelah pembayaran Valid.');
        }

        $order->update(['tracking_number' => $data['tracking_number'], 'order_status' => 'shipped']);

        return back()->with('success', 'Nomor resi tersimpan. Status pesanan menjadi Dikirim.');
    }

    private function own(Order $order): void
    {
        abort_unless($order->vendor_id === auth()->id(), 403);
    }
}
