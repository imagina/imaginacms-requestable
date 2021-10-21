<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestableCategoriesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('requestable__categories', function (Blueprint $table) {
      $table->engine = 'InnoDB';
      $table->increments('id');
      // Your fields...
      $table->string('type');
      $table->integer('time_elapsed_to_cancel')->nullable();
      $table->integer('default_status')->default(1);
      $table->text('events')->nullable();
      $table->text('eta_event')->nullable();
      $table->text('requestable_type')->nullable();
      
      $table->integer('form_id')->unsigned()->nullable();
      $table->foreign('form_id')->references('id')->on('iforms__forms')->onDelete('cascade');
      
      // Audit fields
      $table->timestamps();
      $table->auditStamps();
    });
  }
  
  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('requestable__categories');
  }
}
