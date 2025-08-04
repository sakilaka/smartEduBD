<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentSubjectAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_subject_assigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->index()->constrained();
            $table->foreignId('academic_class_id')->index()->constrained();
            $table->foreignId('student_id')->index()->constrained();
            $table->foreignId('subject_id')->index()->constrained();
            $table->tinyInteger('main_subject')->default(0);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('student_subject_assigns');
    }
}
