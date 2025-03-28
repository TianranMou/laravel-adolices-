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
        Schema::create('site_user', function (Blueprint $table) {
            $table->unsignedInteger('site_id');
            $table->foreign('site_id')->references('site_id')->on('site');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->timestamps();

            $table->primary(['site_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_user');
    }
};
