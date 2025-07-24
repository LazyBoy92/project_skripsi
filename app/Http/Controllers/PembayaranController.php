<?php

namespace App\Http\Controllers;

use App\Models\BeliProdukModel;
use App\Models\PembayaranModel;
use App\Models\ProdukModel;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        $id = session('id');

        $produk = ProdukModel::withWhereHas('produk_beli', function ($query) use ($id) {
            $query->where('user_id', $id);
        })->get();

        return view('pembayaran.bukti_pembayaran', compact('produk'));
    }

    public function metode_pembayaran(Request $request, string $order_id)
    {
        $beli_produk = BeliProdukModel::where('order_id', $order_id)->first();

        if (!$beli_produk) {
            return back()->with('error', 'Order tidak ditemukan.');
        }

        if ($beli_produk->status == 'success') {
            return redirect('/bukti_pembayaran')->with('success', 'Pembayaran sudah berhasil sebelumnya.');
        }

        // Redirect ke invoice Xendit yang sudah dibuat
        return redirect($beli_produk->invoice_url);
    }

    public function download_bukti_pembayaran(string $order_id)
    {
        $produk = ProdukModel::whereHas('produk_beli', function ($query) use ($order_id) {
            $query->where('order_id', $order_id);
        })->with(['produk_beli' => function ($query) use ($order_id) {
            $query->where('order_id', $order_id);
        }])->get();

        $id = session('id');
        $user = User::find($id);

        

        $pembayaran = PembayaranModel::where('order_id', $order_id)->first();
        
        if (!$pembayaran) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $invoice = 'invoice-' . $pembayaran->order_id . '.pdf';

        $pdf = Pdf::loadView('pembayaran.download_bukti_pembayaran', compact('user', 'produk', 'pembayaran'));
        return $pdf->download($invoice);
    }
}
