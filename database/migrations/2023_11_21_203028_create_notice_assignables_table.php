<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeAssignablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice_assignables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notice_id')->constrained();
            $table->foreignId('academic_class_id')->nullable();

            $table->foreign('notice_id')->references('id')->on('notices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notice_assignables');
    }
}
