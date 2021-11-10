<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiverColumnToInboxNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('add_receiver_column_to_inbox_notification');

        // Schema::table('inbox_notification', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('receiver',false,true);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inbox_notification', function (Blueprint $table) {
            //
        });
    }
}
