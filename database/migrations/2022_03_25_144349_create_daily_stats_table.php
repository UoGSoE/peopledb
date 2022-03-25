<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('academics_count')->default(0);
            $table->integer('phd_students_count')->default(0);
            $table->integer('mpas_count')->default(0);
            $table->integer('technicians_count')->default(0);
            $table->integer('total_count')->default(0);
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_stats');
    }
};
