<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilyMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id('member_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->string('name_member');
            $table->date('birth_date_member');
            $table->string('first_name_member');
            $table->foreignId('relation_id')->constrained('family_relation', 'relation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('family_members');
    }
}
