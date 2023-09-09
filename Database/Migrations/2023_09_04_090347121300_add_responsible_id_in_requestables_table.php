<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResponsibleIdInRequestablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('requestable__requestables', function (Blueprint $table) {
        $table->integer('responsible_id')->unsigned()->nullable()->after("requestable_id");
        $table->foreign('responsible_id')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
