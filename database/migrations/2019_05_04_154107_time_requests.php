<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TimeRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_requests', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('lehrer');
            $table->timestamp('target_date');
            $table->integer("denied");
            $table->integer("processed");
            $table->string("requestedByID");
            $table->string("requestedByName");
            $table->timestamp('added')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_requests');
    }
}
