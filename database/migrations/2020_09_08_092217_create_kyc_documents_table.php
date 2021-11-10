<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKycDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('kyc_documents');
        
        Schema::create('kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id',false,true);
            $table->string('document_type');
            $table->binary('image_data');
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
        Schema::dropIfExists('kyc_documents');
    }
}
