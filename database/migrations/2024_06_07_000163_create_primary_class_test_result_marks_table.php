<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrimaryClassTestResultMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('primary_class_test_result_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('primary_class_test_result_details_id');
            $table->foreign('primary_class_test_result_details_id', 'fk_pctr_marks_pctr_id')
                ->references('id')
                ->on('primary_class_test_result_details')
                ->onDelete('cascade');

            $table->foreignId('subject_id')
                ->constrained();
            $table->decimal('mark', 5, 2)->nullable();
            $table->decimal('exam_mark', 5, 2)->nullable();
            $table->decimal('pass_mark', 5, 2)->nullable();
            $table->enum('result_status', ['PASSED', 'FAILED'])->nullable();
            $table->userlog();
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
        Schema::dropIfExists('primary_class_test_result_marks');
    }
}
