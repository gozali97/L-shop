<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET SESSION sql_require_primary_key=0');
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupons_name');
            $table->string('code')->unique();
            $table->enum('type',['fixed','percent'])->default('fixed');
            $table->decimal('value',20,2);
            $table->enum('status',['active','inactive','expired'])->default('inactive');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('coupons');
    }
}
