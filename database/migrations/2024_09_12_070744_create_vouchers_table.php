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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->dateTime("expire_date")->nullable();
            $table->boolean("expired")->default(0);
            $table->integer("quantity")->nullable();
            $table->integer("used")->default(0);
            $table->integer("upc")->comment("Usage Per Customer")->nullable();
            $table->string("fixed_discount")->nullable();
            $table->string("percent_discount")->nullable();
            $table->string("max_discount")->comment("Highest discount that cannot be exceeded (ONLY FOR PERCENT DISCOUNT)")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
