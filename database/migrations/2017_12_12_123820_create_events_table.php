<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->string('description',255);
            $table->boolean('is_private')->default(0);
            $table->string('location');
            $table->string('phone_no');
            $table->string('website_url');
            $table->string('lat');
            $table->string('lng');
            $table->string('timezone');
            $table->string('event_date');
            $table->string('utc_event_time');
            $table->string('event_end_date');
            $table->string('utc_event_end_date');
            $table->integer('view_count')->default(0);
            $table->integer('phone_view_count')->default(0);
            $table->integer('website_view_count')->default(0);
            $table->integer('reoccure')->default(0);
            $table->integer('reoccure_type')->default(1);
            $table->boolean('is_reoccuring')->default(0);
            $table->boolean('is_reoccuring_forever')->default(0);
            $table->string('reoccure_end_date')->nullable();
            $table->string('utc_reoccure_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('events');
    }

}
