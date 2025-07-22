<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidAtToTblBeliProdukTable extends Migration
{
    public function up()
    {
        Schema::table('tbl_beli_produk', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('tbl_beli_produk', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });
    }
}
