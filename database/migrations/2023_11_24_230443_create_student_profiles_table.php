<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('roll_number')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('religion', 20)->nullable();
            $table->date('dob')->nullable();
            $table->string('nid_or_birth_reg', 50)->nullable();
            $table->string('fathers_name_en', 100)->nullable();
            $table->string('fathers_name_bn', 100)->nullable();
            $table->string('fathers_mobile', 15)->nullable();
            $table->string('fathers_nid_or_birth_reg', 50)->nullable();
            $table->string('mothers_name_en', 100)->nullable();
            $table->string('mothers_name_bn', 100)->nullable();
            $table->string('mothers_mobile', 15)->nullable();
            $table->string('mothers_nid_or_birth_reg', 50)->nullable();
            $table->string('permanent_address', 255)->nullable();
            $table->string('present_address', 255)->nullable();
            $table->string('disability', 3)->default('No');
            $table->string('profile')->nullable();
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
        Schema::dropIfExists('student_profiles');
    }
}
