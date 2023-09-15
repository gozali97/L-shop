<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET SESSION sql_require_primary_key=0');
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->float('sub_total');
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->float('coupon')->nullable();
            $table->float('total_amount');
            $table->integer('quantity');
            $table->integer('payment_bank_id');
            $table->enum('payment_method', ['bank-transfer', 'va', 'qris', 'cod'])->default('bank-transfer');
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->string('note')->nullable();
            $table->enum('status', ['pending', 'expired', 'payment-confirm-request', 'processing', 'shipping', 'completed', 'return-request', 'return-approve', 'return-cancel', 'return-reject', 'return-shipping', 'return-completed', 'refund-request', 'refund-approve', 'refund-rejected', 'refund-completed'])->default('pending');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('shipping_id')->references('id')->on('shippings')->onDelete('SET NULL');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('country');
            $table->string('post_code')->nullable();
            $table->text('address1');
            $table->text('address2')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
