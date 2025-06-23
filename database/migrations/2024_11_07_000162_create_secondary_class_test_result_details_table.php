<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecondaryClassTestResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secondary_class_test_result_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('secondary_class_test_result_id');
            $table->foreign('secondary_class_test_result_id', 'fk_sctr_details_sctr_id')
                ->references('id')
                ->on('secondary_class_test_results')
                ->onDelete('cascade');

            $table->foreignId('student_id')->index()->constrained();
            $table->decimal('total_mark', 5, 2)->nullable();
            $table->enum('result_status', ['PASSED', 'FAILED'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('secondary_class_test_result_details');
    }
}
