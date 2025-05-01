<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenant_availabilities', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenant_availabilities');
    }
}; 