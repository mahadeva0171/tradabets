<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('withdraw_requests');
        
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id',false,true);
            $table->string('status');
            $table->float('amount', 5, 2)->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraw_requests');
    }
}
