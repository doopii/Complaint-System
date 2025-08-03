<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id('complaint_id');
            $table->unsignedBigInteger('student_id');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('photo')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps(); // creates created_at and updated_at

            // Foreign key constraint 
            // $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}
