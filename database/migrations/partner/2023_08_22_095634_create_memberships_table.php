<?php

use App\Enums\PackType;
use App\Enums\StripePeriod;
use App\Enums\StripePriceType;
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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('order_id')->unsigned()->nullable(); //parent Order
            $table->bigInteger('order_item_id')->unsigned()->nullable(); //parent OrderItem
            $table->bigInteger('pack_id')->unsigned(); //parent Pack
            $table->bigInteger('pack_price_id')->unsigned(); //parent PackPrice
            $table->enum('type', PackType::all());
            $table->string('title', 255);
            $table->string('sub_title', 255)->nullable();
            $table->text('description')->nullable();
// $table->boolean('is_active')->default(false)->comment('bool'); //global state
            $table->boolean('is_restricted')->default(false)->comment('bool'); //Indicator for book session limitations: off-peak, class types
            $table->json('restrictions')->nullable()->comment('json'); //Object having applicable restrictions by class type
            $table->enum('billing_type', StripePriceType::all())->comment('enum');
            $table->integer('sessions')->unsigned()->default(0)->comment('number'); //of bookings/sessions pack provides for recurring billing cycle OR one_time
            $table->boolean('is_expiring')->default(false)->comment('bool'); //Unused sessions will never expire
            $table->integer('expiration')->unsigned()->nullable()->comment('int'); //of X days, weeks, months, years. Unused sessions will expire after computed value.
            $table->enum('expiration_period', StripePeriod::all())->nullable()->comment('enum, multiply expiration');
            $table->decimal('price', 8, 2)->unsigned()->default(0)->comment('human'); //0 is OK, this is free
            $table->string('currency', 3)->nullable(); // Three-letter ISO currency code, in lowercase. Must be a supported currency.
            $table->string('currency_symbol', 10)->nullable();
            $table->enum('interval', StripePeriod::all())->nullable()->comment('recurring only'); // Billing frequency. Either day, week, month or year. 1 year is max regardless
            $table->integer('interval_count')->unsigned()->nullable()->comment('recurring only'); // The number of intervals between subscription billings
            $table->boolean('is_unlimited')->default(false)->comment('bool'); //If true, plan does not produce any session/credits and subscriber can book sessions without limitations
            $table->boolean('is_fap')->default(false)->comment('bool'); //If true and is_unlimited is also true, bookings are limited by Fair access policy
            $table->integer('fap_value')->unsigned()->default(1)->comment('number'); // number of classes/services member can book for given day when on unlimited subscription
            $table->boolean('is_ongoing')->default(true)->comment('recurring only, bool'); //Subscrption will not stop (true), unless manually cancelled. Stops after X charge cycles (false)
            $table->integer('fixed_count')->unsigned()->nullable()->comment('recurring only'); // The number of billing cycles to complete before subscription termination
            $table->boolean('is_renewable')->default(false)->comment('bool'); //Auto purchase enabled (with saved card) when sessions ran out or expired (one_time only)
            $table->boolean('is_intro')->default(false)->comment('bool'); //Flag inherited from PackPrice
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
