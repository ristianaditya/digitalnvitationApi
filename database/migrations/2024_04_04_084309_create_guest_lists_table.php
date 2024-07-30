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
        Schema::create('guest_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invitation_id')->nullable(false);
            $table->string('name', 100)->nullable(false);
            $table->string('no_phone', 20)->nullable();
            $table->enum('attendance', ['attended', 'absent', 'unconfirmed'])->nullable()->default('unconfirmed');
            $table->timestamp('time_attend', $precision = 0)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('invitation_id')->on('invitations')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_lists');
    }
};
