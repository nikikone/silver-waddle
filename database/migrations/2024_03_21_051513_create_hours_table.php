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
        Schema::create('hours', function (Blueprint $table) {
            $table->id('hour_id');
            $table->foreignId('day_id')->nullable(False)->constrained(
                table: 'days', indexName: 'day_id'
            );
            $table->integer('hour')->nullable(False);
            $table->integer('total')->nullable(True);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hours');
    }
};
