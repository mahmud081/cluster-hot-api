<?php

use App\Models\Network;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('ip_address', 39)->nullable();
            $table->string('mac', 20)->nullable();
            $table->integer('max_value')->default(0);
            $table->string('mqtt_id')->nullable();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Property::class);
            $table->foreignIdFor(Room::class);
            $table->foreignIdFor(Network::class);
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
