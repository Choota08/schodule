<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            // relasi ke users (untuk login)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // kode unik guru (dari Excel: PGJ-0001)
            $table->string('teacher_code')->unique();

            // guru hanya boleh 1 mapel
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
