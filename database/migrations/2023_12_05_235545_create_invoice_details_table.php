<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->index()->constrained();
            $table->foreignId('account_head_id')->index()->constrained();
            $table->foreignId('tuition_fee_generate_details_id')->constrained();
            $table->date('invoice_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status', 15)->default('pending')->comment('pending', 'success');
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
        Schema::dropIfExists('invoice_details');
    }
}
