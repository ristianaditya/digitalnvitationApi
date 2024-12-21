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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->string('project_id', 100)->nullable();
            $table->unsignedBigInteger('music_id')->nullable(false);
            $table->string('invite_link', 100)->nullable(false)->unique('invitations_invite_link_unique');
            $table->string('invite_name', 100)->nullable(false);
            $table->string('clipboard', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->timestamp('invite_date', $precision = 0)->nullable();
            $table->enum('status', ['publish', 'unpublish'])->nullable()->default('unpublish');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('project_id')->on('projects')->references('id');
            $table->foreign('music_id')->on('musics')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
