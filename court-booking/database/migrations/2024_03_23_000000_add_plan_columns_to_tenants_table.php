<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('plan', ['basic', 'premium', 'ultimate'])->default('basic')->after('database_name');
            $table->integer('weekly_booking_limit')->default(2)->after('plan');
            $table->boolean('can_book_weekends')->default(false)->after('weekly_booking_limit');
            $table->boolean('can_reschedule')->default(false)->after('can_book_weekends');
            $table->integer('advance_booking_days')->default(7)->after('can_reschedule');
        });
    }

    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'plan',
                'weekly_booking_limit',
                'can_book_weekends',
                'can_reschedule',
                'advance_booking_days'
            ]);
        });
    }
}; 