<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mixpost_facebook_insights', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->integer('type');
            $table->integer('value');
            $table->date('date');
            $table->timestamps();

            $table->unique(['account_id', 'type', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mixpost_facebook_insights');
    }
};
