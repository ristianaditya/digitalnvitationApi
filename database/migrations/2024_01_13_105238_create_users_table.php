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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->nullable(false)->unique('users_username_unique');
            $table->string('email', 100)->nullable(false)->unique('users_email_unique');
            $table->string('password', 100)->nullable(false);
            $table->string('name', 100)->nullable(false);
            $table->string('no_phone', 20)->nullable(false)->unique('users_phone_unique');
            $table->enum('gender', ['male', 'female'])->nullable(false)->default('male');
            $table->enum('role', ['admin', 'user'])->nullable(false)->default('user');
            $table->enum('subscribe', ['starter', 'silver', 'gold'])->nullable()->default('starter');
            $table->timestamp('subscribe_start', $precision = 0)->nullable();
            $table->timestamp('subscribe_expired', $precision = 0)->nullable();
            $table->string('token', 100)->nullable()->unique('users_token_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
