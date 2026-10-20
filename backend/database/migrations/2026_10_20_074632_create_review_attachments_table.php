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
        Schema::create('review_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('review_id');

            $table->string('status')->default('pending');
            $table->string('original_path')->nullable();
            $table->string('processed_path')->nullable();
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_attachments');
    }
};
