<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWalletsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('litecoin_address')->nullable()->default(null);
            $table->string('litecoin_secret')->nullable()->default(null);
            $table->string('ripple_address')->nullable()->default(null);
            $table->string('ripple_secret')->nullable()->default(null);
            $table->string('peercoin_address')->nullable()->default(null);
            $table->string('peercoin_secret')->nullable()->default(null);
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
            $table->dropColumn('litecoin_address');
            $table->dropColumn('litecoin_secret');
            $table->dropColumn('ripple_address');
            $table->dropColumn('ripple_secret');
            $table->dropColumn('peercoin_address');
            $table->dropColumn('peercoin_secret');
        });
    }
}
