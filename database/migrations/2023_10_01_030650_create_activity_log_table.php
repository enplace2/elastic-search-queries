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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('activity_types');
        Schema::enableForeignKeyConstraints();

        Schema::create('activity_types', function (Blueprint $table){
            $table->id();
            $table->string('string_identifier')->unique();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('performed_by_user_id');
            $table->nullableMorphs('model');
            $table->json('properties');
            $table->unsignedBigInteger('activity_type_id');
            $table->timestamps();

            $table->foreign('activity_type_id')
                ->references('id')
                ->on('activity_types');

            $table->foreign('performed_by_user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('activity_types');
        Schema::enableForeignKeyConstraints();
    }
};
