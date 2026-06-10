<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe_transasctions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subscribe_package_id')->nullable();
            $table->string('bill_code')->nullable()->comment('ToyyibPay bill code');
            $table->string('pay_id')->nullable()->comment('gateway transaction id');
            $table->string('status')->default('pending')->comment('pending|success|failed');
            $table->string('transaction_description')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            $table->index('bill_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscribe_transasctions');
    }
};
