<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->enum('plan', ['basic', 'premium', 'ultimate'])->default('basic');
            $table->integer('weekly_booking_count')->default(0);
            $table->integer('reschedule_count')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}; 