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
        Schema::create('leave_card', function (Blueprint $table) {
            $table->bigIncrements('leavecardid');
            $table->string('personnel_id')->nullable();
            $table->string('PERIOD')->nullable();
            $table->string('PARTICULARS')->nullable();
            $table->string('VL_EARNED')->nullable();
            $table->string('VL_ABSENCE_UNDERTIMEWITHPAY')->nullable();
            $table->string('VL_BALANCE')->nullable();
            $table->string('VL_ABSENCE_UNDERTIMEWITHOUTPAY')->nullable();
            $table->string('SL_EARNED')->nullable();
            $table->string('SL_ABSENCE_UNDERTIMEWITHPAY')->nullable();
            $table->string('SL_BALANCE')->nullable();
            $table->string('SL_ABSENCE_UNDERTIMEWITHOUTPAY')->nullable();
            $table->string('CTO_EARNED_HRS')->nullable();
            $table->string('CTO_ABSENCE_UNDERTIMEWITHPAY_HRS')->nullable();
            $table->string('CTO_BALANCE_HRS')->nullable();
            $table->string('CTO_REMARK')->nullable();
            $table->datetime('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('leave_card');
    }
};
