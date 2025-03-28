<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubventionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subvention', function (Blueprint $table) {
            $table->id('subvention_id');
            $table->unsignedInteger('state_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreign('state_id')->references('state_id')->on('state_sub');
            $table->string('name_asso');
            $table->string('RIB');
            $table->decimal('montant', 8, 2);
            $table->string('link_attestation')->nullable();
            $table->string('motif_refus')->nullable();
            $table->date('payment_subvention')->nullable();
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
        Schema::dropIfExists('subvention');
    }
}
