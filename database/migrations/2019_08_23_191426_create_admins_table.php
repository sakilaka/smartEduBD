<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('role_id')->index();
            $table->foreignId('institution_id')->index()->nullable();
            $table->string('name', 60);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('mobile', 50)->nullable();
            $table->string('profile')->nullable();
            $table->boolean('block')->default(0);
            $table->enum('status', ['draft', 'active', 'deactive'])->default('active');
            $table->rememberToken();
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
        Schema::dropIfExists('admins');
    }
}
