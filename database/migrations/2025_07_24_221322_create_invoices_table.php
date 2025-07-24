<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // migration-nya:
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users');
    $table->string('nama_produk');
    $table->integer('qty');
    $table->integer('harga_satuan');
    $table->integer('total');
    $table->string('no_hp');
    $table->string('alamat');
    $table->string('invoice_number')->unique();
    $table->timestamp('tanggal_transaksi')->useCurrent();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
