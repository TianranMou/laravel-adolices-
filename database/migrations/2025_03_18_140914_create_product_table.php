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
        // Drop the existing table if it exists
        Schema::dropIfExists('product');

        // Create the new table
        Schema::create('product', function (Blueprint $table) {
            $table->id('product_id');
            $table->unsignedBigInteger('quota_id');
            $table->foreign('quota_id')->references('quota_id')->on('quota');

            $table->unsignedInteger('shop_id');
            $table->foreign('shop_id')->references('shop_id')->on('shop');

            $table->string('withdrawal_method', 250);
            $table->string('product_name', 250);
            $table->double('subsidized_price');
            $table->double('price');
            $table->boolean('dematerialized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
