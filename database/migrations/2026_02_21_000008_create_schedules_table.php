<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_room_id')
                  ->constrained('class_rooms')
                  ->cascadeOnDelete();

            $table->foreignId('session_id')
                  ->constrained('sessions')
                  ->cascadeOnDelete();

            $table->enum('day', [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            ]);

            $table->timestamps();

            $table->unique(['class_room_id', 'session_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
