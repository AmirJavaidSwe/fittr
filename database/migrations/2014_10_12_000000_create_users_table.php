<?php

use App\Enums\AppUserRole;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', AppUserRole::roles());
            $table->string('name');
            $table->string('email')->unique();
            $table->string('stripe_customer_id', 255)->nullable(); //customer id (partner is client => service subscriptions)
            $table->string('stripe_account_id', 255)->nullable(); //connected account of partner
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('db_host', 512)->nullable(); //Same server or someother managed db
            $table->integer('db_port')->nullable(); //Default 3306
            $table->string('db_name', 64)->nullable(); //Must begin with a letter. Subsequent characters can be letters, underscores, or digits
            $table->string('db_user', 16)->nullable(); // 1-16chars, Starts with a letter
            $table->string('db_password', 256)->nullable();
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
};
