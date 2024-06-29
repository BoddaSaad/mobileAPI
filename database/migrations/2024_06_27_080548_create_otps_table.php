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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('otp');
            $table->string('email');
            $table->tinyInteger('usage')
                ->default(0)
                ->comment('0=> Email Verification, 1=> Password Reset');
            $table->tinyInteger('used')
                ->default(0)
                ->comment('0=> False, 1=> True');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
