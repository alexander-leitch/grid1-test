<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->boolean('checked')->default(false);
            $table->longText('description')->nullable();
            $table->string('interest')->nullable();
            $table->dateTime('date_of_birth')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('account')->nullable();
            
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
