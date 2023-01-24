<?php

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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0);
            $table->boolean('is_private')->default(0)->comment('private for admins');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->decimal('tx_percent', 5, 2)->unsigned()->default(0)->comment('0-100%');
            $table->decimal('tx_fixed_fee', 5, 2)->unsigned()->default(0.2)->comment('£');
            $table->decimal('fee_annually', 8, 2)->unsigned()->default(0)->comment('£, per month, billed annually');
            $table->decimal('fee_monthly', 8, 2)->unsigned()->default(0)->comment('£, per month, billed monthly');
            $table->decimal('monthly_fee_sites', 8, 2)->unsigned()->default(0)->comment('£, per site above 1');
            $table->tinyInteger('admin_users')->unsigned()->default(0)->comment('0: unlimited');
            $table->tinyInteger('max_sites')->unsigned()->default(0)->comment('0: unlimited');
            $table->timestamps();
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
        Schema::dropIfExists('packages');
    }
};
