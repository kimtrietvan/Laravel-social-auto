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
        Schema::create('pinterest_boards', function (Blueprint $table) {
            $table->id();
            $table->integer('pinterest_id')->unsigned();
            $table->foreign('pinterest_id')->references('id')->on('pinterest_account')->onDelete('cascade');
            $table->string('board_id')->unique();
            $table->string('board_name')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinterest_boards');
    }
};
