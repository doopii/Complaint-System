<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommunityFeaturesToComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->boolean('is_public')->default(true); // Allow students to make complaints public
            $table->boolean('is_anonymous')->default(false); // Allow anonymous posting
            $table->integer('upvotes')->default(0); // Community upvoting
            $table->text('tags')->nullable(); // Hashtags for categorization
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'is_anonymous', 'upvotes', 'tags']);
        });
    }
}
