<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ERP işçi ID-lərini PortalWebsite-dakı köhnə worker ID-ləri ilə 1:1 bərabərləşdirir.
     * Bu sayədə PortalWebsite heç bir sinxronizasiya olmadan birbaşa ERP-dən işləyəcək
     * və keçmiş minlərlə satış (sales) pozulmayacaq.
     */
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        DB::transaction(function () {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            $employees = DB::table('employees')
                ->whereNotNull('portal_worker_id')
                ->where('portal_worker_id', '>', 0)
                ->whereRaw('id != portal_worker_id')
                ->get(['id', 'portal_worker_id']);

            $offset = 1000000;

            if ($employees->isNotEmpty()) {
                // 1-ci Mərhələ: Müvəqqəti offset ilə ID toqquşmasının qarşısını alırıq
                foreach ($employees as $emp) {
                    $oldId = $emp->id;
                    $tempId = (int)$emp->portal_worker_id + $offset;

                    DB::table('employee_branches')->where('employee_id', $oldId)->update(['employee_id' => $tempId]);
                    DB::table('employee_games')->where('employee_id', $oldId)->update(['employee_id' => $tempId]);
                    DB::table('employee_status_histories')->where('employee_id', $oldId)->update(['employee_id' => $tempId]);
                    DB::table('employee_worker_types')->where('employee_id', $oldId)->update(['employee_id' => $tempId]);
                    DB::table('employees')->where('id', $oldId)->update(['id' => $tempId]);
                }

                // 2-ci Mərhələ: Offset-i çıxarıb dəqiq portal_worker_id edirik
                foreach ($employees as $emp) {
                    $tempId = (int)$emp->portal_worker_id + $offset;
                    $finalId = (int)$emp->portal_worker_id;

                    DB::table('employee_branches')->where('employee_id', $tempId)->update(['employee_id' => $finalId]);
                    DB::table('employee_games')->where('employee_id', $tempId)->update(['employee_id' => $finalId]);
                    DB::table('employee_status_histories')->where('employee_id', $tempId)->update(['employee_id' => $finalId]);
                    DB::table('employee_worker_types')->where('employee_id', $tempId)->update(['employee_id' => $finalId]);
                    DB::table('employees')->where('id', $tempId)->update(['id' => $finalId]);
                }
            }

            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        });

        // DDL (ALTER TABLE) MySQL-də tranzaksiyanı avtomatik commit edir, ona görə transaction-dan kənarda çağırılmalıdır
        if (DB::getDriverName() === 'mysql') {
            $maxId = (int)DB::table('employees')->max('id');
            $nextAuto = max($maxId + 1, 500);
            DB::statement("ALTER TABLE employees AUTO_INCREMENT = {$nextAuto};");
        }
    }

    public function down(): void
    {
        // 1:1 bərabərləşdirmə geriyə çevrilmir
    }
};
