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
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('appointment_id')->constrained("appointments")->onUpdate('cascade')->onDelete('cascade');
            $table->integer('status')->default(0);
            $table->time('delay_time')->default(0);
            $table->integer('number')->nullable();
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('queue_id')->constrained('queues');
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
        Schema::dropIfExists('booking');
    }
};
