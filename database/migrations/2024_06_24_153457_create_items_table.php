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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("name_ar");
            $table->text("description");
            $table->text("description_ar");
            $table->tinyInteger("active")->default(1);
            $table->integer("discount")->default(0);
            $table->integer("price");
            $table->string("location");
            $table->string("location_link");
            $table->string("phone");
            $table->foreignId('category_id')->constrained(table: 'categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
