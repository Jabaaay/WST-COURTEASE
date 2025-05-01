<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('plan_type')->nullable()->after('is_premium');
            $table->timestamp('plan_started_at')->nullable()->after('plan_type');
        });
    }

    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['plan_type', 'plan_started_at']);
        });
    }
}; 