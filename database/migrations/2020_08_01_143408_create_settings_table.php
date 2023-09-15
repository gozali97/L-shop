<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET SESSION sql_require_primary_key=0');
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->text('brand_name');
            $table->longText('description');
            $table->text('short_des');
            $table->string('brand_logo');
            $table->string('photo');
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->text('socmed_facebook');
            $table->text('socmed_instagram');
            $table->string('socmed_wa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
