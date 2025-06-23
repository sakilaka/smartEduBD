<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuardiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('current_student_id')->nullable();
            $table->string('name_en', 100);
            $table->string('name_bn', 100)->nullable();
            $table->string('mobile', 30)->index()->unique();
            $table->string('email', 50)->nullable();
            $table->string('nid_or_birth_reg', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('relations', 50)->nullable();
            $table->string('password');
            $table->integer('otp')->nullable();
            $table->enum('status', ['draft', 'active', 'deactive'])->default('draft');
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
        Schema::dropIfExists('guardians');
    }
}
