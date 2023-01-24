<?php

use App\Enums\Instance;
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
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->integer('partner_id');
            $table->enum('status', Instance::statuses())->default(Instance::INACTIVE->value);
            $table->ipAddress('public_ip')->nullable();
            $table->ipAddress('private_ip')->nullable();
            $table->string('subdomain', 100)->nullable();
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
        Schema::dropIfExists('instances');
    }
};
