<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->index()->constrained();
            $table->foreignId('academic_class_id')->index()->constrained();
            $table->string('title',)->nullable();
            $table->enum('provider', ['SSL', 'SPAY'])->default('SSL');
            $table->json('options')->nullable();
            $table->string('description')->nullable();
            $table->enum('status', ['draft', 'active', 'deactive'])->default('active');
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
        Schema::dropIfExists('payment_gateways');
    }
}
