<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->index()->constrained();
            $table->string('file_type', 20)->default('Image')->comment("Image / PDF");
            $table->string('type', 20)->default('public')->comment("public / office / student");
            $table->tinyInteger('all_class')->default(0);
            $table->date('date');
            $table->string('title');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->integer('notice_from')->nullable();
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
        Schema::dropIfExists('notices');
    }
}
