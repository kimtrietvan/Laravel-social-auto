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
        Schema::create('schedule_woocommerce_products', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->foreign('product_id')->references('id')->on('woocommerce_products')->onDelete('cascade');
            $table->string('message')->nullable(true);
            $table->string('title')->nullable(true);
            $table->string('links')->nullable(true);
            $table->string('mediaType'); 
            $table->string('accountType');
            $table->integer('account_id');
            $table->integer('board_id')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_woocommerce_products');
    }
};
