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
        Schema::create('minutes', function (Blueprint $table) {
            $table->id('minute_id');
            $table->foreignId('hour_id')->nullable(False)->constrained(
                table: 'hour', indexName: 'hour_id'
            );
            $table->integer('minute')->nullable(False);
            $table->integer('fullness')->nullable(False);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minutes');
    }
};
