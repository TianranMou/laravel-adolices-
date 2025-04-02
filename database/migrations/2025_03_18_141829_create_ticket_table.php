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
        Schema::create('ticket', function (Blueprint $table) {
            $table->id('ticket_id');

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('product_id')->on('product')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('site_id');
            $table->foreign('site_id')->references('site_id')->on('site')->onDelete('cascade');

            $table->string('ticket_link')->nullable();
            $table->string('partner_code')->nullable();
            $table->string('partner_id')->nullable();
            $table->date('validity_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket');
    }
};
