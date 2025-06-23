<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecondaryResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secondary_result_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secondary_result_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->index()->constrained();
            $table->integer('roll_number')->nullable();
            $table->decimal('total_mark', 5, 2)->nullable();
            $table->decimal('gpa', 5, 2)->nullable();
            $table->string('letter_grade', 5)->nullable();
            $table->decimal('total_mark_without_additional', 5, 2)->nullable();
            $table->decimal('gpa_without_additional', 5, 2)->nullable();
            $table->enum('result_status', ['PASSED', 'FAILED'])->nullable();
            $table->integer('merit_position_in_section')->nullable();
            $table->integer('merit_position_in_shift')->nullable();
            $table->integer('merit_position_in_class')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('secondary_result_details');
    }
}
