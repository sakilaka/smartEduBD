<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->index()->constrained();
            $table->foreignId('academic_session_id')->index()->constrained();
            $table->foreignId('academic_class_id')->index()->constrained();
            $table->foreignId('student_id')->index()->constrained();
            $table->foreignId('account_head_id')->index()->constrained();
            $table->integer('amount');
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
        Schema::dropIfExists('student_discounts');
    }
}
