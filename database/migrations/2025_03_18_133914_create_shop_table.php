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
        Schema::create('shop', function (Blueprint $table) {
            $table->increments('shop_id');
            $table->string('shop_name', 100);
            $table->text('short_description');
            $table->text('long_description')->nullable();
            $table->integer('min_limit')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('thumbnail', 250)->nullable();
            $table->string('doc_link', 250)->nullable();
            $table->string('bc_link', 250)->nullable();
            $table->string('ha_link', 250)->nullable();
            $table->string('photo_link', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop');
    }
};
