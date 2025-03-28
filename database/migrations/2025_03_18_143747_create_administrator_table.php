php
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
        Schema::create('administrator', function (Blueprint $table) {
            // This will be a composite primary key table
            $table->unsignedInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            
            // Define composite primary key
            $table->primary(['shop_id', 'user_id']);
            
            // Define foreign keys
            $table->foreign('shop_id')
                  ->references('shop_id')
                  ->on('shop')
                  ->onDelete('cascade');
            
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrator');
    }
};