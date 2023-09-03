<?php

use App\Enums\PackType;
use App\Enums\StateType;
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
        Schema::create('session_credits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('order_id')->unsigned()->nullable(); //parent Order
            $table->bigInteger('order_item_id')->unsigned()->nullable(); //parent OrderItem
            $table->bigInteger('membership_id')->unsigned()->nullable(); //parent Membership
            $table->enum('status', StateType::all())->default(StateType::get('inactive'));
            $table->enum('type', PackType::creditTypes()); //matched pack type, onу of creditable types (corp, default excluded)
            $table->decimal('price_value', 8, 2)->unsigned()->default(0)->comment('avg'); // avg price value of single credit
            $table->json('restrictions')->nullable()->comment('json'); //Object having applicable restrictions (location, class type, service type, off-peak only)
            $table->text('notes_user')->nullable();
            $table->text('notes_internal')->nullable();
            $table->timestamp('expiry_at')->nullable(); //never expire if null
            //note: same credit came be used/refunded multiple times, capture last state
            $table->timestamp('used_at')->nullable(); //same as booking created_at
            $table->timestamp('refunded_at')->nullable();//same as booking cancelled_at
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_credits');
    }
};
