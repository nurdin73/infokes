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
        Schema::create('payment_medical_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->foreignId('medical_registration_id')->constrained('medical_registrations')->cascadeOnDelete();
            $table->string('payment_type');
            $table->string('card_no')->nullable();
            $table->string('paid_by')->nullable();
            $table->bigInteger('price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_medical_registrations');
    }
};
