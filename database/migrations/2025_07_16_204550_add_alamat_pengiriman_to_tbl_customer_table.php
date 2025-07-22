<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tbl_customer', function (Blueprint $table) {
            $table->text('alamat_pengiriman')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('tbl_customer', function (Blueprint $table) {
            $table->dropColumn('alamat_pengiriman');
        });
    }
    
};
