<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 40);
            $table->string('phone', 20)->unique();
            $table->string('password');
            $table->string('city', 20)->nullable();
            $table->text('address')->nullable();;
            $table->text('avatar')->nullable();;
            $table->unsignedInteger('hospital_id');
            $table->timestamps();

            $table->foreign('hospital_id')
                  ->references('id')->on('hospitals')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
