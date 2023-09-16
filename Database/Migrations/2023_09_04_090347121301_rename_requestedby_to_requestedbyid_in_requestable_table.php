<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameRequestedByToRequestedByIdInRequestableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasColumn('requestable__requestables','requested_by')) {
       
            Schema::table('requestable__requestables', function (Blueprint $table) {
                $table->renameColumn("requested_by","requested_by_id");
            });

        }
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

