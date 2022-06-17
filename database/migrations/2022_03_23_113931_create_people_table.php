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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('surname');
            $table->string('forenames');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->string('group')->nullable();
            $table->unsignedBigInteger('reports_to')->nullable();
            $table->foreign('reports_to')->references('id')->on('people');
            $table->unsignedBigInteger('people_type_id')->nullable();
            $table->foreign('people_type_id')->references('id')->on('people_types');
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
        Schema::dropIfExists('people');
    }
};
