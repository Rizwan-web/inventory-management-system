<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_detail', function (Blueprint $table) {
            $table->increments('id_sales_detail');
            $table->integer('id_sales');
            $table->integer('id_product');
            $table->integer('selling_price');
            $table->integer('jumlah');
            $table->tinyInteger('discount')->default(0);
            $table->integer('subtotal');
            $table->integer('invoice_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_detail');
    }
}
