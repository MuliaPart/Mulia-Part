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
        Schema::create('cash_histories', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');

            $table->decimal('total_omset', 15, 2)->default(0);
            $table->decimal('total_jasa', 15, 2)->default(0);
            $table->decimal('total_sparepart', 15, 2)->default(0);
            $table->decimal('total_diskon', 15, 2)->default(0);
            $table->decimal('total_hutang', 15, 2)->default(0);
            $table->decimal('total_transfer', 15, 2)->default(0);
            $table->decimal('total_cash', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_histories');
    }
};
