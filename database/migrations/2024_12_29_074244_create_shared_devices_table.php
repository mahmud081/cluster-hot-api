<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shared_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sharer_id');
            $table->unsignedBigInteger('sharee_id');
            $table->unsignedBigInteger('device_id');
            $table->tinyInteger('role')->default(1);
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('sharer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sharee_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_devices');
    }
};
