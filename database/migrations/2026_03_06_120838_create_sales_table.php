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
    Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->string('category')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->integer('stock_awal')->default(0);
            $table->integer('terjual')->default(0);
            $table->integer('stock_akhir')->default(0);
            $table->bigInteger('total_harga_pokok')->default(0);
            $table->bigInteger('total_harga_jual')->default(0);
            $table->bigInteger('total_laba')->default(0);

            $table->timestamps();
        });
    }       

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }

};
