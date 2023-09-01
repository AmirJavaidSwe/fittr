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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('pack_price_id')->unsigned()->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->integer('quantity')->unsigned()->default(0);
            $table->string('currency', 3)->nullable(); //ISO 3 chars
            $table->integer('unit_amount')->unsigned()->default(0); //stripe integer, Single unit price (q-ty of 1)
            $table->integer('amount_discount')->unsigned()->default(0); //stripe integer, Total discount amount applied
            $table->integer('amount_subtotal')->unsigned()->default(0); //stripe integer, Total before any discounts or taxes are applied
            $table->integer('amount_total')->unsigned()->default(0); //stripe integer, Total after discounts and taxes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
