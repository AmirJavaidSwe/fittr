<?php

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
            $table->string('export_type', 100); // App\Enums\ExportType
            $table->text('filters')->nullable();
            $table->integer('created_by')->unsigned(); //creator ID
            $table->string('csv_file_name', 1024)->nullable();
            $table->integer('file_rows')->unsigned()->nullable();
            $table->integer('file_size')->unsigned()->nullable(); //bytes (4,294,967,295 max)
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 100)->default('pending'); //pending, processing, completed, failed
            $table->string('messages', 100)->default('pending'); //pending, processing, completed, failed
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
