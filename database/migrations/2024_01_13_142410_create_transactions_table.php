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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('id_payment', 100)->nullable();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->string('amount', 100)->nullable(false);
            $table->enum('status', ['success', 'failed', 'pending'])->nullable(false)->default('pending');
            $table->enum('subscribe', ['DigitalDazzle', 'SparkleSend', 'DigitalDreamscape'])->nullable(false)->default('starter');
            $table->timestamp('subscribe_start', $precision = 0)->nullable();
            $table->timestamp('subscribe_expired', $precision = 0)->nullable();
            $table->string('midtrans_token', 100)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
