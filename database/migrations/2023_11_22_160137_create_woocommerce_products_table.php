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
        Schema::create('woocommerce_products', function (Blueprint $table) {
            $table->id();
            $table->integer('woocommerce_id');
            $table->foreign('woocommerce_id')->references('id')->on('woocommerces')->onDelete('cascade');
            $table->integer('product_code');
            $table->string('product_name');
            $table->string('product_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woocommerce_products');
    }
};
