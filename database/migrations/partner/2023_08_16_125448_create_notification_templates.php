<?php

use App\Enums\NotificationMailDriver;
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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('key', 255); // Can be used to categorize templates
            $table->string('sg_template_name', 255)->nullable(); // for sendgrid
            $table->string('sg_template_id', 255)->nullable(); // for sendgrid
            $table->json('placeholders')->nullable();
            $table->enum('mail_driver', NotificationMailDriver::all())->default(NotificationMailDriver::get('smtp'));
            $table->string('from_name', 255);
            $table->string('from_email', 255);
            $table->string('subject', 255);
            $table->text('content')->nullable();
            $table->text('content_plain')->nullable();
            $table->string('content_sms', 160)->nullable();
            $table->boolean('unsubscribe')->default(false);
            $table->boolean('bypass')->default(false);
            $table->boolean('status')->default(true);
            $table->boolean('readonly')->default(false);
            $table->string('notes', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_tempalate');
    }
};
