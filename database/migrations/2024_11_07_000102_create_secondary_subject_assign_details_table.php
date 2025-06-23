<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecondarySubjectAssignDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secondary_subject_assign_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secondary_subject_assign_id')
                ->constrained('secondary_subject_assigns', 'id')
                ->onDelete('cascade')
                ->index('fk_secondary_assign_id');

            $table->foreignId('subject_id');
            $table->tinyInteger('fourth_subject')->default(0);
            $table->decimal('ct_mark', 5, 2)->nullable();
            $table->decimal('ct_pass_mark', 5, 2)->nullable();
            $table->decimal('cq_mark', 5, 2)->nullable();
            $table->decimal('cq_pass_mark', 5, 2)->nullable();
            $table->decimal('mcq_mark', 5, 2)->nullable();
            $table->decimal('mcq_pass_mark', 5, 2)->nullable();
            $table->decimal('converted_mark', 5, 2)->nullable();
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
        Schema::dropIfExists('secondary_subject_assign_details');
    }
}
