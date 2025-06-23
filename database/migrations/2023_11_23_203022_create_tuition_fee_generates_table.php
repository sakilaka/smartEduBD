<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTuitionFeeGeneratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tuition_fee_generates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->index()->constrained();
            $table->foreignId('shift_id')->index()->constrained();
            $table->foreignId('medium_id')->index()->constrained('mediums');
            $table->foreignId('academic_class_id')->index()->constrained();
            $table->foreignId('group_id')->index()->constrained();
            $table->foreignId('account_head_id')->index()->constrained();
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
        Schema::dropIfExists('tuition_fee_generates');
    }
}
