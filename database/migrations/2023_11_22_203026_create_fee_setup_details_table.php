<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeeSetupDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_setup_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_setup_id')->index()->constrained();
            $table->foreignId('account_head_id')->index()->constrained();
            $table->foreignId('payment_gateway_id')->index()->constrained();
            $table->string('payment_duration', 10)->default('Yearly')->comment('Yearly / Monthly / Any Time');
            $table->tinyInteger('payment_priority')->default(0);
            $table->bigInteger('depend_head_id')->nullable();
            $table->tinyInteger('online_addmission_fees')->default(0);
            $table->tinyInteger('school_fees')->default(0);
            $table->tinyInteger('service_charge')->default(0);
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->date('additional_date')->nullable();
            $table->integer('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_setup_details');
    }
}
