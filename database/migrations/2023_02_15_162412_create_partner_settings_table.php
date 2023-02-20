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
            $table->enum('key', SettingKey::all());
            $table->enum('group_name', SettingGroup::all());
            $table->enum('cast_to', CastType::all())->default(CastType::string->name);
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
