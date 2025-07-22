<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tbl_screenshots_produk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produk_id');
            $table->string('folder')->nullable(); // sesuaikan dengan kebutuhan
            $table->timestamps();

            // Foreign key (optional, jika produk_id dari tabel produk)
            // $table->foreign('produk_id')->references('id')->on('tbl_produk')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tbl_screenshots_produk');
    }
};
