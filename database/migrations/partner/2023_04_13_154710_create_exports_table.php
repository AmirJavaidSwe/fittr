<?php

use App\Enums\ExportStatus;
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
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100); // App\Enums\ExportType
            $table->text('filters')->nullable();
            $table->integer('created_by')->unsigned(); //creator ID
            $table->string('file_name', 1024)->nullable();
            $table->integer('file_rows')->unsigned()->nullable();
            $table->integer('file_size')->unsigned()->nullable(); //bytes (4,294,967,295 max)
            $table->string('file_type')->default('csv')->nullable(); //bytes (4,294,967,295 max)
            $table->string('file_mime_type')->nullable();
            $table->string('file_path')->nullable();
            $table->string('storage_disk')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 100)->default(ExportStatus::pending->name); //pending, processing, completed, failed
            $table->string('messages')->nullable();
            $table->string('token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
};
