<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->string('username');
            $table->string('email',191)->unique()->nullable();
            $table->string('password')->nullable();
            $table->enum('login_type', ['fb', 'twitter','email']);
            $table->enum('device_type', ['ios', 'andriod','web']);
            $table->string('device_id')->nullable();
            $table->string('fb_id')->nullable();
            $table->string('twitter_id')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('location')->nullable();
            $table->string('time_zone')->nullable();
            $table->string('photo')->nullable();
            $table->string('cover')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->boolean('is_banned')->default(0);
            $table->string('email_code')->nullable();
            $table->string('session_token')->nullable();
            $table->boolean('is_private')->default(0);
            $table->boolean('get_notifications')->default(1);
            $table->boolean('fb_connected')->default(0);
            $table->boolean('twitter_connected')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
