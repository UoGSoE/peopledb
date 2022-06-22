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
        Schema::create('people_task', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('people_id');
            $table->foreign('people_id')->references('id')->on('people');
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('people_task');
    }
};
