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
            $table->string('name', 100)->nullable();
            $table->string('no_phone', 20)->nullable()->unique('users_phone_unique');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('role', ['admin', 'user'])->nullable()->default('user');
            $table->string('address')->nullable();
            $table->string('picture')->nullable();
            $table->timestamps();

            $table->softDeletes();
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
