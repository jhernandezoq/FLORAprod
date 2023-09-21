<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('cedula',20);
            $table->string('name',50);
            $table->string('first_name',50);
            $table->string('last_name',50);
            $table->unsignedBigInteger('profile_id')->nullable();
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('ubication_id');
            $table->string('email')->nullable();
            $table->string('extension',10)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('email_aux')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->dateTime('start_date', 0);
            $table->dateTime('birth_date', 0);
            $table->dateTime('end_date', 0)->nullable();
            $table->string('password');
            $table->boolean('active');
            $table->rememberToken();

            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('profiles');
            $table->foreign('position_id')->references('id')->on('positions');
            $table->foreign('ubication_id')->references('id')->on('ubications');
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
