<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index()->nullable();
            $table->foreignId('account_head_id')->index()->constrained();
            $table->foreignId('payment_gateway_id')->constrained();

            $table->foreignId('institution_id')->index()->constrained();
            $table->foreignId('academic_session_id')->index()->constrained();
            $table->foreignId('campus_id')->index()->constrained();
            $table->foreignId('shift_id')->index()->constrained();
            $table->foreignId('medium_id')->index()->constrained('mediums');
            $table->foreignId('academic_class_id')->index()->constrained();
            $table->foreignId('group_id')->index()->constrained();
            $table->foreignId('section_id')->index()->constrained();

            $table->string('invoice_type', 20)->comment('tuition, school');
            $table->date('invoice_date');
            $table->bigInteger('invoice_number')->unique();
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->decimal('service_charge', 10, 2)->nullable();

            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_method', 10)->default("SSL");
            $table->string('card_type', 100)->nullable();
            $table->string('bank_trans_id')->nullable();
            $table->string('gateway_order_id')->nullable();

            $table->string('created_from', 15)->default('web');
            $table->string('status', 15)->default('pending');
            $table->timestamps();
            $table->userlog();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
