<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Processed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('processed', function (Blueprint $table) {
        $table->id();
        $table->string('name')->nullable();
        $table->string('file_path')->nullable();
        $table->integer('completed_rows')->default(0);
        $table->boolean('completed')->default(false);
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
              Schema::dropIfExists('processed');
    }
}
