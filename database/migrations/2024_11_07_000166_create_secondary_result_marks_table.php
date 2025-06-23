<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecondaryResultMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secondary_result_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secondary_result_details_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->index()->constrained();
            $table->decimal('ct_mark', 5, 2)->nullable();
            $table->decimal('cq_mark', 5, 2)->nullable();
            $table->decimal('mcq_mark', 5, 2)->nullable();
            $table->decimal('practical_mark', 5, 2)->nullable();
            $table->decimal('obtained_mark', 5, 2)->nullable();
            $table->decimal('total_mark', 5, 2)->nullable();
            $table->decimal('gpa', 5, 2)->nullable();
            $table->string('letter_grade', 5)->nullable();

            $table->foreignId('combined_result_marks_id')->nullable();
            $table->decimal('combined_mark', 5, 2)->nullable();
            $table->decimal('combined_gpa', 5, 2)->nullable();
            $table->string('combined_letter_grade', 5)->nullable();

            $table->json('pass_marks')->nullable();
            $table->tinyInteger('fourth_subject')->default(0);
            $table->tinyInteger('highest_marks')->default(0);
            $table->tinyInteger('sorting')->default(0);

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
        Schema::dropIfExists('secondary_result_marks');
    }
}
