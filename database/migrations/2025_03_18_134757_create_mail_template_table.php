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
        Schema::create('template_mail', function (Blueprint $table) {
            $table->increments('mail_template_id');
            $table->unsignedInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('shop_id')->on('shop')->onDelete('set null');
            $table->string('subject', 100);
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_mail');
    }
};
