<?php

use App\Enums\StripePriceType;
use App\Enums\StripePeriod;
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
        Schema::create('pack_prices', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_price_id');
            $table->enum('type', StripePriceType::all())->comment('enum'); // one_time or recurring
            $table->boolean('is_active')->default(false)->comment('bool');
            $table->bigInteger('priceable_id')->unsigned();
            $table->string('priceable_type', 255); // AppServiceProvider@boot() model as string => membership_pack|...
            $table->decimal('price', 8, 2)->unsigned()->default(0)->comment('human'); //0 is OK, this is free
            $table->integer('unit_amount')->unsigned()->default(0)->comment('stripe'); // multiplied by 100 except for zero-decimal currencies
            $table->integer('sessions')->unsigned()->default(0)->comment('number'); //of bookings/sessions pack provides for recurring billing cycle OR one_time
            $table->boolean('is_expiring')->default(false)->comment('bool'); //Unused sessions will never expire
            $table->integer('expiration')->unsigned()->nullable()->comment('int'); //of X days, weeks, months, years. Unused sessions will expire after computed value.
            $table->enum('expiration_period', StripePeriod::all())->nullable()->comment('enum, multiply expiration');
            $table->string('currency', 3)->nullable(); // Three-letter ISO currency code, in lowercase. Must be a supported currency.
            $table->enum('interval', StripePeriod::all())->nullable()->comment('recurring only'); // Billing frequency. Either day, week, month or year. 1 year is max regardless
            $table->integer('interval_count')->unsigned()->nullable()->comment('recurring only'); // The number of intervals between subscription billings
            $table->boolean('is_ongoing')->default(true)->comment('recurring only, bool'); //Subscrption will not stop (true), unless manually cancelled. Stops after X charge cycles (false)
            $table->integer('fixed_count')->unsigned()->nullable()->comment('recurring only'); // The number of billing cycles to complete before subscription termination
            $table->boolean('is_renewable')->default(false)->comment('bool'); //Auto purchase enabled (with saved card) when sessions ran out or expired (one_time only)
            $table->boolean('is_intro')->default(false)->comment('bool'); //Available to members never made a purchase before. If true, is_renewable should be set false (one_time only)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pack_prices');
    }
};