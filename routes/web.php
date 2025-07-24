<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\XenditController;
use App\Http\Middleware\OnlyCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->middleware('prevent.customer');

    // Produk
    Route::resource('menu_produk', ProductController::class)->middleware('prevent.customer');
    Route::get('/produk/{id_produk}', [ProductController::class, 'show'])->name('produk.show');
    Route::get('/produk/{id_produk}/beli', [XenditController::class, 'createInvoice'])->name('produk.beli');
    Route::post('/produk/{id}/beli', [XenditController::class, 'createInvoice'])->name('produk.beli');
    Route::get('/produk_terjual', [ProductController::class, 'produk_terjual'])->middleware('prevent.customer');
    Route::get('/beli/{id}', [ProductController::class, 'beli'])->name('beli')->middleware('only.customer');
    Route::post('/proses_checkout', [ProductController::class, 'proses_checkout']);
    Route::get('/download_produk/{id_produk}', [ProductController::class, 'download_produk'])->name('download_produk')->middleware('reset.headers');
    Route::get('/produk-search', [ProductController::class, 'search'])->name('produk.search');

    // Customer
    Route::get('/profile_customer/{id}', [CustomerController::class, 'index'])->middleware('check.id.customer');
    Route::post('/update_profile', [CustomerController::class, 'update_profile']);

    // Pengaturan
    Route::get('/ganti_password', [PengaturanController::class, 'index']);
    Route::post('/proses_ganti_password', [PengaturanController::class, 'proses_ganti_password']);
    Route::get('/extract_screenshots', [PengaturanController::class, 'extract_screenshots'])->middleware('prevent.customer');
    Route::post('/proses_extract_screenshots', [PengaturanController::class, 'proses_extract_screenshots'])->middleware('prevent.customer');

    // Pembayaran
    Route::middleware([OnlyCustomer::class])->group(function () {
        Route::get('/download_bukti_pembayaran/{order_id}', [PembayaranController::class, 'download_bukti_pembayaran'])->name('download_bukti_pembayaran');
        Route::get('/bukti_pembayaran', [PembayaranController::class, 'index']);
        Route::get('/metode_pembayaran/{order_id}', [PembayaranController::class, 'metode_pembayaran'])->name('metode_pembayaran');
    });

    // Xendit Test
    Route::get('/bayar-xendit', [XenditController::class, 'createInvoice']);

    // Logout
    Route::get('/logout', [AuthController::class, 'logout']);

    // Test
    Route::get('/test', function () {
        return 'Tes berhasil';
    });
});

// Webhook Xendit
Route::post('/xendit/webhook', [XenditController::class, 'callback']);

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::get('/pendaftaran', [AuthController::class, 'pendaftaran']);
    Route::get('/redirect', [AuthController::class, 'redirect']);
    Route::get('/auth/google/callback', [AuthController::class, 'callback']);

    Route::post('/proses_login', [AuthController::class, 'proses_login'])->middleware('throttle:limit_login');
    Route::post('/proses_pendaftaran', [AuthController::class, 'proses_pendaftaran']);
    Route::post('/proses_lupa_password', [AuthController::class, 'proses_lupa_password']);

    Route::get('/lupa_password', function () {
        return view('auth.lupa_password');
    })->name('password.request');

    Route::post('/lupa_password', function (Request $request) {
        $request->validate(['email' => 'required|email'], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Email tidak valid.'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => 'Link reset password telah berhasil dikirim melalui email. Silakan periksa email Anda.'])
            : back()->withErrors(['email' => 'Email tidak ada.']);
    })->name('password.email');

    Route::get('/reset_password/{token}', function (string $token) {
        return view('auth.reset_password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset_password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:10|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.required' => 'Password harus di isi.',
            'password.min' => 'Password setidaknya minimal 10 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password harus di isi.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    })->name('password.update');
});
