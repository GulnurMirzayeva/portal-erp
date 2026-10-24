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
        Schema::table('attendance_branches', function (Blueprint $table) {
            $table->time('work_time_weekdays_start')->nullable()->after('longitude');
            $table->time('work_time_weekdays_end')->nullable()->after('work_time_weekdays_start');
            $table->time('work_time_weekend_start')->nullable()->after('work_time_weekdays_end');
            $table->time('work_time_weekend_end')->nullable()->after('work_time_weekend_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_branches', function (Blueprint $table) {
            $table->dropColumn([
                'work_time_weekdays_start',
                'work_time_weekdays_end',
                'work_time_weekend_start',
                'work_time_weekend_end',
            ]);
        });
    }
};
