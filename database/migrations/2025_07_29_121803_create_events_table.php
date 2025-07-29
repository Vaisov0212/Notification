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
        Schema::create('events', function (Blueprint $table) {
           $table->id();
            $table->bigInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->string('colors');
            $table->string('repeat_type'); // daily, weekly, monthly
            $table->integer('repeat_interval'); // for daily: every N days
            $table->json('repeat_days_moth'); // for weekly: ['monday', 'friday'], for monthly: [1, 15, 30]
            $table->json('start_date'); // event times: ['09:00', '14:30', '18:00']
            $table->json('end_date'); // agar kerak bo'lsa ishlatiladi
            $table->string('status')->default('active'); // active, inactive, etc.
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
