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
        Schema::create('familydatas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invitation_id')->nullable(false);
            $table->string('personFirst', 100)->nullable();
            $table->enum('genderFirst', ['male', 'female'])->nullable();
            $table->string('personSecond', 100)->nullable();
            $table->enum('genderSecond', ['male', 'female'])->nullable();
            $table->string('fatherMale', 100)->nullable();
            $table->string('motherMale', 100)->nullable();
            $table->string('fatherFemale', 100)->nullable();
            $table->string('motherFemale', 100)->nullable();
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
        Schema::dropIfExists('familydatas');
    }
};
