<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecondaryGradeManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secondary_grade_management', function (Blueprint $table) {
            $table->id();
            $table->decimal('from_mark', 10, 2);
            $table->decimal('to_mark', 10, 2);
            $table->string('grade', 5);
            $table->decimal('gpa', 10, 2);
            $table->decimal('from_gpa', 10, 2);
            $table->decimal('to_gpa', 10, 2);
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
        Schema::dropIfExists('secondary_grade_management');
    }
}
