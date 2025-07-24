<?php

namespace App\Http\Controllers;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SimpleInvoiceController extends Controller
{
    public function store(Request $request, $id_produk)
{
    $produk = ProdukModel::findOrFail($id_produk);
    $qty = max((int)$request->input('qty', 1), 1);
    $harga = (int)str_replace('.', '', $produk->harga);
    $total = $qty * $harga;

    $user = auth()->user();
    $invoice = Invoice::create([
        'user_id' => $user->id,
        'nama_produk' => $produk->nama,
        'qty' => $qty,
        'harga_satuan' => $harga,
        'total' => $total,
        'no_hp' => $user->customer->no_hp ?? '08123456789',
        'alamat' => $user->customer->alamat_pengiriman ?? 'Alamat belum diisi',
        'invoice_number' => 'INV-' . time(),
    ]);

    return redirect()->route('invoice.pdf', $invoice->id);
}

public function pdf($id)
{
    $invoice = Invoice::findOrFail($id);
    $pdf = Pdf::loadView('invoice.simple_pdf', compact('invoice'));
    return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
}
}
