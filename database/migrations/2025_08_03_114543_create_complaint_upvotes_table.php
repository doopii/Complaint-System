<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintUpvotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaint_upvotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complaint_id');
            $table->string('student_id'); // Who upvoted
            $table->timestamps();
            
            // Prevent duplicate upvotes
            $table->unique(['complaint_id', 'student_id']);
            
            // Foreign key constraints
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
        Schema::dropIfExists('complaint_upvotes');
    }
}
