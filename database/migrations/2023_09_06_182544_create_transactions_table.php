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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        $table->string('transaction_name');
        $table->decimal('transaction_value', 8, 2);
        $table->string('transaction_bank');
        $table->date('transaction_date');
        $table->string('transaction_wa');
        $table->string('transaction_file');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('transactions');
}
    };
