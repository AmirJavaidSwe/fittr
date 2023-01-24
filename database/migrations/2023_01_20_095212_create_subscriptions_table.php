<?php

use App\Enums\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->enum('status', Subscription::statuses());
            $table->string('package_title', 255);
            $table->integer('package_id');
            $table->integer('partner_id');
            $table->enum('cycle', Subscription::periods());
            $table->decimal('fee', 8, 2)->unsigned()->default(0)->comment('charged per cycle');
            $table->decimal('tx_percent', 5, 2)->unsigned()->default(0)->comment('0-100%');
            $table->decimal('tx_fixed_fee', 5, 2)->unsigned()->default(0.2)->comment('£');
            $table->decimal('monthly_fee_sites', 8, 2)->unsigned()->default(0)->comment('£, per site above 1');
            $table->tinyInteger('admin_users')->unsigned()->default(0)->comment('0: unlimited');
            $table->tinyInteger('max_sites')->unsigned()->default(0)->comment('0: unlimited');
            $table->timestamps();
            $table->timestamp('cancelled_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
