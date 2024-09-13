<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference');
            $table->string('transaction')->nullable();
            $table->string('status')->nullable();
            $table->string('voucher')->nullable();
            $table->decimal('price', total: 8, places: 2)->comment("Price in cents");
            $table->decimal('final_price', total: 8, places: 2)->comment("Final price in cents");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
