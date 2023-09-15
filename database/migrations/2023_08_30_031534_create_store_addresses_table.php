<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET SESSION sql_require_primary_key=0');
        Schema::create('store_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('full_address');
            $table->integer('province_id');
            $table->integer('city_id');
            $table->integer('district_id');
            $table->string('postal_code');
            $table->string('default');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_addresses');
    }
};
