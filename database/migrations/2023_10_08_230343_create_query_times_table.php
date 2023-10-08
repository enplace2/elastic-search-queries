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
        Schema::create('query_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('query_type_id');
            $table->foreign('query_type_id')->references('id')->on('query_types')->onDelete('cascade');
            $table->float('time_in_ms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('query_times');
    }
};
