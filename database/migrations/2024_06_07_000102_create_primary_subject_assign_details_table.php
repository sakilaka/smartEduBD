<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrimarySubjectAssignDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('primary_subject_assign_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_subject_assign_id')->index()->constrained();
            $table->foreignId('subject_id');
            $table->tinyInteger('fourth_subject')->default(0);
            $table->decimal('ct_mark', 5, 2)->nullable();
            $table->decimal('ct_pass_mark', 5, 2)->nullable();
            $table->decimal('cq_mark', 5, 2)->nullable();
            $table->decimal('cq_pass_mark', 5, 2)->nullable();
            $table->decimal('mcq_mark', 5, 2)->nullable();
            $table->decimal('mcq_pass_mark', 5, 2)->nullable();
            $table->decimal('practical_mark', 5, 2)->nullable();
            $table->decimal('practical_pass_mark', 5, 2)->nullable();
            $table->decimal('total_mark', 5, 2)->nullable();
            $table->tinyInteger('sorting')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('primary_subject_assign_details');
    }
}
