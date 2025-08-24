<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('comment_id')->primary();  
            $table->unsignedBigInteger('complaint_id');
            $table->string('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->string('username');
            $table->text('comment_text');
            $table->timestamps();

            $table->foreign('complaint_id')->references('complaint_id')->on('complaints')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
