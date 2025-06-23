<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrimaryClassTestResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('primary_class_test_result_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('primary_class_test_result_id');
            $table->foreign('primary_class_test_result_id', 'fk_pctr_details_pctr_id')
                ->references('id')
                ->on('primary_class_test_results')
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
        Schema::dropIfExists('primary_class_test_result_details');
    }
}
