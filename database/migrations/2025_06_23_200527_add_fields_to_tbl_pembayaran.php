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
        Schema::table('tbl_pembayaran', function (Blueprint $table) {
            $table->unsignedBigInteger('produk_id')->after('order_id');
            $table->string('status')->default('pending')->after('total');
            $table->timestamps(); // jika ingin pakai created_at & updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pembayaran', function (Blueprint $table) {
            //
        });
    }
};
