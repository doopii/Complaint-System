<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePhoneFromStudentsAndAdminsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
        
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
        });
        
        Schema::table('admins', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
        });
    }
}
