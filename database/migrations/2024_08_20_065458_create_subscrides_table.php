<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A "subscribe" record represents a user's active/expired subscription
     * instance to a package (SaaS access to the WMS).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subscribe_package_id')->nullable();
            $table->string('period')->nullable()->comment('snapshot of the package title');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('status')->default('pending')->comment('pending|active|expired');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscribes');
    }
};
