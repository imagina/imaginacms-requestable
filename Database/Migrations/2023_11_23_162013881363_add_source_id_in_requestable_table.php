<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceIdInRequestableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requestable__requestables', function (Blueprint $table) {

            $table->integer('source_id')->unsigned()->nullable()->after("responsible_id");
            $table->foreign('source_id')->references('id')->on('requestable__sources')->onDelete('restrict');
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
