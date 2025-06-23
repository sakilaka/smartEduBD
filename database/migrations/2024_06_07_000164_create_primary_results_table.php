<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrimaryResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('primary_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->index()->constrained();
            $table->foreignId('institution_id')->index()->constrained();
            $table->foreignId('campus_id')->index()->constrained();
            $table->foreignId('shift_id')->index()->constrained();
            $table->foreignId('medium_id')->index()->constrained('mediums');
            $table->foreignId('academic_class_id')->index()->constrained();
            $table->foreignId('group_id')->index()->constrained();
            $table->foreignId('section_id')->index()->constrained();

            $table->foreignId('exam_id')->index()->constrained();
            $table->date('published_date')->nullable();
            $table->enum('status', ['draft', 'published', 'deactive'])->default('draft');
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
        Schema::dropIfExists('primary_results');
    }
}
