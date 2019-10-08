<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfileVisibleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('profile_visible')->default(true);
            $table->boolean('activated')->default(false); //has not ever logged in yet
            $table->boolean('banned')->default(false); //banned by admin
            $table->boolean('online')->default(false); //online/offline
            $table->boolean('enable_chat')->default(true); //possiblity of chat
            $table->boolean('enable_email')->default(true); //possiblity of email
            $table->boolean('enable_calendar')->default(true); //possiblity of calendar
            $table->boolean('enable_wallet')->default(true); //possiblity of litecoin
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_visible');
            $table->dropColumn('activated');
            $table->dropColumn('banned');
            $table->dropColumn('online');
        });
    }
}
