<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {   
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('kode_user')->unique();
            $table->string('name');
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('profile_photo')->nullable(); // ✅ FOTO DI SINI
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
