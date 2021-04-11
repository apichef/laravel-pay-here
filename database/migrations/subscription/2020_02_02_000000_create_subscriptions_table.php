<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable()->unique();
            $table->string('subscription_id')->nullable()->unique();
            $table->float('amount');
            $table->string('currency');
            $table->tinyInteger('status')->default(0);
            $table->string('recurrence');
            $table->string('duration');
            $table->tinyInteger('recurrence_status')->nullable();
            $table->date('next_recurrence_date')->nullable();
            $table->tinyInteger('times_paid')->default(0);
            $table->morphs('subscribable');
            $table->morphs('payer');
            $table->boolean('validated')->nullable();
            $table->json('summary')->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
}
