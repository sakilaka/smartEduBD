<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->index()->constrained();
            $table->foreignId('academic_session_id')->index()->constrained();
            $table->foreignId('campus_id')->index()->constrained();
            $table->foreignId('shift_id')->index()->constrained();
            $table->foreignId('medium_id')->index()->constrained('mediums');
            $table->foreignId('academic_class_id')->index()->constrained();
            $table->foreignId('group_id')->index()->constrained();
            $table->foreignId('section_id')->index()->constrained();
            $table->foreignId('guardian_id')->index()->constrained();

            $table->integer('software_id')->index()->unique();
            $table->string('name_en', 100);
            $table->string('name_bn', 100)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('password');
            $table->integer('otp')->nullable();
            $table->enum('status', ['draft', 'active', 'deactive'])->default('draft');
            $table->string('created_from', 15)->default('admin');
            $table->string('updated_from', 15)->default('admin');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
