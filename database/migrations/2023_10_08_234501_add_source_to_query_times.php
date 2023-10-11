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
        Schema::table('query_times', function (Blueprint $table) {
            $table->enum('source', ['elasticsearch', 'mysql'])->after('time_in_ms');
            $table->integer('total_records_at_run_time');
            $table->json('query_results');
            $table->integer('shards')->nullable();
            $table->integer('number_of_records_returned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('query_times', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('total_records_at_run_time');
            $table->dropColumn('query_results');
            $table->dropColumn('shards');
        });
    }
};
