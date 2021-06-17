<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestableRequestablesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('requestable__requestables', function (Blueprint $table) {
      $table->engine = 'InnoDB';
      
      $table->increments('id');
      
      $table->string('requestable_type');
      $table->string('requestable_id');
      
      $table->string('type');
      $table->integer('status')->default(0)->unsigned();
  
      $table->text('fields')->nullable();
      
      $table->timestamp('eta')->nullable(); //estimated time of accomplishment
      
      $table->integer('created_by')
        ->unsigned()
        ->nullable();
      
      $table->foreign('created_by')
        ->references('id')
        ->on(config('auth.table', 'users'))
        ->onDelete('restrict');
      
      $table->integer('reviewed_by')
        ->unsigned()
        ->nullable();
      
      $table->foreign('reviewed_by')
        ->references('id')
        ->on(config('auth.table', 'users'))
        ->onDelete('restrict');
      
      // Your fields
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
    Schema::dropIfExists('requestable__requestables');
  }
}
