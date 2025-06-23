<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable();
            $table->string('name');
            $table->string('short_name', 20);
            $table->string('domain')->nullable();
            $table->string('logo')->nullable();
            $table->string('address');
            $table->integer('activation_date')->nullable();
            $table->integer('expired_date')->nullable();

            $table->string('idcard_front_part')->nullable();
            $table->string('idcard_back_part')->nullable();
            $table->string('admit_card_image')->nullable();
            $table->string('primary_term_marksheet')->nullable();
            $table->string('secondary_term_marksheet')->nullable();
            $table->string('secondary_term_marksheet_combined')->nullable();
            $table->float('ssl_service_charge_percent')->nullable();
            $table->float('spay_service_charge_percent')->nullable();

            $table->enum('status', ['draft', 'active', 'deactive'])->default('active');
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
        Schema::dropIfExists('institutions');
    }
}
