<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukModel;
use App\Models\BeliProdukModel;
use App\Models\PembayaranModel;
use Xendit\Xendit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class XenditController extends Controller
{
    public function createInvoice(Request $request, $id)
{
    $produk = ProdukModel::findOrFail($id);
    $qty = max((int) $request->input('qty', 1), 1); // pastikan minimal 1
    $totalHarga = $qty * (int) str_replace('.', '', $produk->harga);

    $orderId = 'ORDER-' . auth()->id() . '-' . uniqid();
    $alamat = optional(auth()->user()->customer)->alamat_pengiriman ?? 'Alamat tidak tersedia';
    $noHp   = optional(auth()->user()->customer)->no_hp ?? '08123456789';

   
    DB::beginTransaction();
try {
    $beli = BeliProdukModel::create([
        'user_id'     => auth()->id(),
        'produk_id'   => $produk->id,
        'qty'         => $qty,
        'status'      => 'pending',
        'invoice_url' => '',
        'order_id'    => $orderId,
        'paid_at'     => now(),
    ]);

    Xendit::setApiKey(env('XENDIT_SECRET_KEY'));

    $invoice = \Xendit\Invoice::create([
        'external_id' => $orderId,
        'payer_email' => auth()->user()->email,
        'description' => $produk->nama . " x{$qty}\nAlamat Pembeli: {$alamat}\nPenjual: Darisem (083846449309)",
        'amount'      => $totalHarga,
        'customer' => [
            'given_names'   => auth()->user()->name ?? 'Customer',
            'email'         => auth()->user()->email,
            'mobile_number' => $noHp,
            'address' => [[
                'country'       => 'Indonesia',
                'street_line1'  => $alamat,
            ]]
        ]
    ]);

  
    PembayaranModel::create([
        'order_id'   => $orderId,
        'user_id'    => auth()->id(),
        'produk_id'  => $produk->id, 
        'total'      => $totalHarga,
        'paid_at'    => now(),
        'metode'     => 'Xendit',
    ]);

    $beli->update(['invoice_url' => $invoice['invoice_url']]);

    DB::commit();
    return redirect($invoice['invoice_url']);

} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Gagal membuat invoice Xendit: ' . $e->getMessage());
    return back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
}

}

    

    public function callback(Request $request)
    {
        $data = $request->all();
        Log::info('Webhook received', $data);

        $externalId = $data['external_id'] ?? null;

        if (!$externalId || ($data['status'] ?? '') !== 'PAID') {
            Log::warning('Invalid webhook data', ['external_id' => $externalId, 'status' => $data['status'] ?? null]);
            return response()->json(['message' => 'Invalid data'], 400);
        }

        $order = BeliProdukModel::where('order_id', $externalId)->first();

        if (!$order) {
            Log::error('Order not found in DB', ['external_id' => $externalId]);
            return response()->json(['message' => 'Order not found'], 200);
        }

        $order->update([
            'status'   => 'success',
            'paid_at'  => now(),
        ]);

        Log::info('Order updated', ['order_id' => $externalId]);
        return response()->json(['message' => 'Payment updated'], 200);
    }
}
