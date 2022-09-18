<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_contractor', function (Blueprint $table) {
            $table->string('address', 500)->nullable()->change();
            $table->string('address2', 500)->nullable()->change();
            $table->string('zip', 20)->nullable()->change();
            $table->string('city', 100)->nullable()->change();
            $table->string('nip', 50)->nullable()->change();
            $table->string('phone', 25)->nullable()->change();
            $table->string('mail', 150)->nullable()->change();
            $table->integer('deleted')->default(0)->change();
            $table->text('available_users')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_contractor', function (Blueprint $table) {
            //
        });
    }
};
