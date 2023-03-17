<?php

use App\Enums\SettingGroup;
use App\Enums\SettingKey;
use App\Enums\CastType;
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
        Schema::create('partner_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('partner_id');
            $table->string('key', 100);// App\Enums\SettingKey
            $table->string('group_name', 100); // App\Enums\SettingGroup
            $table->enum('cast_to', CastType::all())->default(CastType::string->name);
            $table->boolean('is_encrypted')->default(false);
            $table->text('val')->nullable();
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
        Schema::dropIfExists('partner_settings');
    }
};
