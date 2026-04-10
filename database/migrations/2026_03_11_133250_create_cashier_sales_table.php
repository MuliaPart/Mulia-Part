<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_sales', function (Blueprint $table) {

            $table->id();

            $table->string('category');
            $table->string('code');

            $table->string('name');

            $table->integer('cost_price');     // harga pokok
            $table->integer('qty');            // jumlah barang

            $table->integer('sell_price');     // harga jual per produk
            $table->integer('total_price');    // total harga

            $table->timestamp('paid_at')->nullable(); // waktu bayar

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_sales');
    }
};
