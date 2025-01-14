<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('stripe_id')->unique();
            $table->string('stripe_payment_intent');
            $table->string('stripe_price');
            $table->string('stripe_status');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->index(['user_id', 'stripe_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
