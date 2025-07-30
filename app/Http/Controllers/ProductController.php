<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\ProdukModel;
use App\Models\User;
use App\Models\BeliProdukModel;
use App\Models\ScreenshotsProdukModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $messages = [
        'nama.required' => 'Nama tidak boleh kosong.',
        'deskripsi.required' => 'Deskripsi tidak boleh kosong.',
        'status.required' => 'Status tidak boleh kosong.',
        'harga.required' => 'Harga tidak boleh kosong.',
        'harga.numeric' => 'Inputan harga harus berupa angka.',
        'harga.max_digits' => 'Nominal harga tidak boleh lebih dari 10 digit.',
    ];

    protected function setSessionFlash($detectMessage, $message)
    {
        Session::flash($detectMessage, $message);
    }

    public function index()
    {
        $user_id = session('id');
        $role = User::find($user_id);

        if ($role->role_id == 1) {
            $semuaProduk = ProdukModel::all();
            return view('produk.index', ['produk' => $semuaProduk]);
        } else if ($role->role_id == 2) {
            $produkCustomer = ProdukModel::select(
                'tbl_produk.id AS id_produk',
                'tbl_produk.nama AS nama_produk',
                'tbl_produk.gambar AS gambar',
                'tbl_produk.deskripsi AS deskripsi_produk',
                'tbl_produk.harga AS harga_produk',
                'tbl_produk.status AS status_produk',
                'tbl_produk.created_at AS tanggal_buat',
                'tbl_produk.updated_at AS tanggal_ubah'
            )
            ->leftJoin('tbl_beli_produk', function ($join) use ($user_id) {
                $join->on('tbl_produk.id', '=', 'tbl_beli_produk.produk_id')
                     ->where('tbl_beli_produk.user_id', '=', $user_id);
            })
            ->distinct()
            ->get();

           


            return view('produk.index', ['produk' => $produkCustomer]);
        }
    }

    public function show(string $id_produk)
    {
        $produk = ProdukModel::findOrFail($id_produk);
        $produkScreenshots = ScreenshotsProdukModel::where('produk_id', $id_produk)->first();
    
        if ($produkScreenshots) {
            $folderExtract = basename($produkScreenshots->folder);
            $directory = public_path('assets/produk_images/' . $folderExtract);
            $ImagesArray = [];
            $file_display = ['jpg', 'jpeg', 'png'];
    
            if (is_dir($directory)) {
                foreach (scandir($directory) as $file) {
                    $file_type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($file_type, $file_display)) {
                        $ImagesArray[] = $file;
                    }
                }
            }
            
    
            $getFiles = $ImagesArray;
            return view('produk.show', compact('produk', 'getFiles', 'folderExtract'));
        } else {
            $msg = (Auth::user()->role_id == 1
                ? 'Upload zip terlebih dahulu yang berisi file screenshots.'
                : 'Admin belum melakukan upload screenshots.');
            $this->setSessionFlash('error', $msg);
            return redirect('/menu_produk');
        }
    }

    public function search(Request $request)
{
    $q = $request->input('q');
    $user_id = session('id');
    $user = User::find($user_id);

    if ($user->role_id == 1) {
        // Admin: tampilkan semua produk yang cocok
        $produk = ProdukModel::where('nama', 'like', "%{$q}%")
            ->orWhere('deskripsi', 'like', "%{$q}%")
            ->get();
    } else {
        // Customer: hanya produk yang dibeli user atau tampil sesuai role
        $produk = ProdukModel::select(
            'tbl_produk.id AS id_produk',
            'tbl_produk.nama AS nama_produk',
            'tbl_produk.gambar AS gambar',
            'tbl_produk.deskripsi AS deskripsi_produk',
            'tbl_produk.harga AS harga_produk',
            'tbl_produk.status AS status_produk',
            'tbl_produk.created_at AS tanggal_buat',
            'tbl_produk.updated_at AS tanggal_ubah'
        )
        ->where(function ($query) use ($q) {
            $query->where('tbl_produk.nama', 'like', "%{$q}%")
                  ->orWhere('tbl_produk.deskripsi', 'like', "%{$q}%");
        })
        ->leftJoin('tbl_beli_produk', function ($join) use ($user_id) {
            $join->on('tbl_produk.id', '=', 'tbl_beli_produk.produk_id')
                 ->where('tbl_beli_produk.user_id', '=', $user_id);
        })
        ->distinct()
        ->get();
    }

    return view('produk.index', compact('produk'));
}

public function produk_terjual()
{
    $produkTerjual = \App\Models\ProdukModel::withSum(['produk_beli as total_terjual' => function ($query) {
        $query->where('status', 'success');
    }], 'qty')->get();
    

        return view('produk.produk_terjual', ['produk' => $produkTerjual]);
}

public function create()
{
    return view('produk.create');
}



public function store(Request $request)
{
    $request->validate([
        'nama' => 'required',
        'deskripsi' => 'required',
        'harga' => 'required|numeric',
        'status' => 'required',
        'gambar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $originalName = pathinfo($request->file('gambar')->getClientOriginalName(), PATHINFO_FILENAME);
    
    $safeName = Str::slug($originalName);
    
    $extension = $request->file('gambar')->getClientOriginalExtension();

    $folderPath = public_path('assets/produk_images/' . $safeName);
    $filePath = $folderPath . '/' . $safeName . '.' . $extension;

    if (!File::exists($folderPath)) {
        File::makeDirectory($folderPath, 0755, true);
    }

    $request->file('gambar')->move($folderPath, $safeName . '.' . $extension);

    ProdukModel::create([
        'nama' => $request->nama,
        'deskripsi' => $request->deskripsi,
        'harga' => $request->harga,
        'status' => $request->status,
        'gambar' => $safeName, 
    ]);

    return redirect()->route('menu_produk.index')->with('success', 'Produk berhasil ditambahkan.');
}



public function destroy($id)
{
    $produk = ProdukModel::findOrFail($id);
    $produk->delete();

    return redirect()->back()->with('success', 'Produk berhasil dihapus.');
}
   


public function edit($id)
{
    $produk = ProdukModel::findOrFail($id);
    return view('produk.edit', compact('produk'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'deskripsi' => 'required|string',
        'harga' => 'required|numeric',
        'status' => 'required|string',
        'gambar' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    ]);

    $produk = ProdukModel::findOrFail($id);

    $produk->nama = $request->nama;
    $produk->deskripsi = $request->deskripsi;
    $produk->harga = $request->harga;
    $produk->status = $request->status;

    if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $folderPath = public_path('assets/produk_images/' . $filename);

        // Hapus folder lama jika berbeda
        if ($produk->gambar !== $filename && file_exists(public_path('assets/produk_images/' . $produk->gambar))) {
            \File::deleteDirectory(public_path('assets/produk_images/' . $produk->gambar));
        }

        // Buat folder baru
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $file->move($folderPath, $filename . '.' . $extension);

        // Simpan nama tanpa ekstensi
        $produk->gambar = $filename;
    }

    $produk->save();

    return redirect()->route('menu_produk.index')->with('success', 'Produk berhasil diperbarui');
}




}
