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
        Schema::create('orderdetails', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('order_no')->unique();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->integer('paymentmethod_id');
            $table->string('total')->nullable();
            $table->string('trx_id')->nullable();
            $table->string('driving_through_fee')->nullable();
            $table->date('date');
            $table->string('time');
            $table->string('timestamp');
            $table->enum('order_type', ['preorder', 'drive_through']);
            $table->date('preorder_date')->nullable();
            $table->string('preorder_time')->nullable();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->string('status')->default('pending');
            $table->string('user_arrival_status')->nullable();
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
        Schema::dropIfExists('orderdetails');
    }
};
