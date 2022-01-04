<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaystackTransferInitiateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paystack_transfer_initiate', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('reference');
            $table->string('amount_in_kobo');
            $table->string('reason')->nullable;
            $table->string('status');
            $table->string('transfer_code');
            $table->string('createdAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paystack_transfer_initiate');
    }
}
