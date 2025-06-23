<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTuitionFeeGenerateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tuition_fee_generate_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuition_fee_generate_id')->constrained();
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->tinyInteger('status')->default(1)->comment('1 = active', '0 = deactive');
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
        Schema::dropIfExists('tuition_fee_generate_details');
    }
}
