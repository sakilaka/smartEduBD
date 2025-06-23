<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecondaryClassTestResultMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secondary_class_test_result_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('secondary_class_test_result_details_id');
            $table->foreign('secondary_class_test_result_details_id', 'fk_sctr_marks_sctr_id')
                ->references('id')
                ->on('secondary_class_test_result_details')
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
        Schema::dropIfExists('secondary_class_test_result_marks');
    }
}
