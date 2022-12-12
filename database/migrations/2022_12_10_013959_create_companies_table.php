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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            //$table->string('email')->unique();
            $table->foreignId('address_id')->constrained('address');
            $table->foreignId('category_id')->constrained('categories');
           // $table->string('phone_number')->nullable();
           // $table->foreignId('role_id')->constrained('roles');
            $table->string('logo')->nullable();
            $table->string('description')->nullable();
            $table->integer('type');
            $table->timestamps();
            $table->enum('role_id', array('1','2'))->default('0');
            $table->foreign('role_id')->references('id')->on('roles');
        });

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
