<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('bills', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users');
      $table->integer('number');
      $table->datetime('date');
      $table->string('from_name');
      $table->string('from_document');
      $table->string('to_name');
      $table->string('to_document');
      $table->float('subtotal', 16,2)->default(0);
      $table->float('tax', 16,2)->default(0);
      $table->float('total', 16,2)->default(0);
      $table->json('items');
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
    Schema::dropIfExists('bills');
  }
}
