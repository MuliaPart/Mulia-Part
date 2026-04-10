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
        Schema::create('sales_histories', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->integer('terjual')->default(0);
            $table->bigInteger('total_harga_pokok')->default(0);
            $table->bigInteger('total_harga_jual')->default(0);
            $table->bigInteger('total_laba')->default(0);
            $table->timestamps();
        });
    } // ← pastikan ini ADA

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_histories');
    }
};
