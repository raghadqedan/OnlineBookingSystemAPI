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
        Schema::create('services_queues', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('queue_id')->constrained('queues');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services_queues');
    }
};
