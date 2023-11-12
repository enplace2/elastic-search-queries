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
        Schema::create('average_query_times', function (Blueprint $table) {
            $table->id();
            $table->double('mean_time');
            $table->double('median_time');
            $table->unsignedBigInteger('query_type_id');
            $table->string('query_type_string_identifier');
            $table->text('query_type_description');
            $table->enum('db_source', ['elasticsearch', 'mysql']);
            $table->double('mean_results_returned');
            $table->timestamps();

            $table->foreign('query_type_id')->references('id')->on('query_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('average_query_times');
    }
};
