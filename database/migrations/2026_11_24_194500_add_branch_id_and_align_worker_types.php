<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Employees cədvəlinə birbaşa branch_id əlavə edirik (PortalWebsite sürətli sorğuları üçün)
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'branch_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('is_intern')->index();
            });

            // Mövcud işçilərin branch_id-lərini doldururuq
            $branches = DB::table('attendance_branches')->pluck('portal_branch_id', 'id');
            $empBranches = DB::table('employee_branches')->where('is_primary', true)->get();
            foreach ($empBranches as $eb) {
                $portalBranchId = $branches[$eb->attendance_branch_id] ?? null;
                if ($portalBranchId) {
                    DB::table('employees')->where('id', $eb->employee_id)->update(['branch_id' => $portalBranchId]);
                }
            }
        }

        // 1.1 Employees cədvəlinə virtual 'name' sütunu əlavə edirik (köhnə kodların $worker->name və pluck('name') dəstəyi üçün)
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'name')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE employees ADD COLUMN name VARCHAR(255) GENERATED ALWAYS AS (full_name) VIRTUAL AFTER full_name;');
            } else {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('name')->nullable()->after('full_name');
                });
            }
        }

        // 2. worker_types ID-lərini portal_worker_type_id ilə 1:1 bərabərləşdiririk
        if (Schema::hasTable('worker_types')) {
            DB::transaction(function () {
                if (DB::getDriverName() === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                }

                $types = DB::table('worker_types')
                    ->whereNotNull('portal_worker_type_id')
                    ->where('portal_worker_type_id', '>', 0)
                    ->whereRaw('id != portal_worker_type_id')
                    ->get(['id', 'portal_worker_type_id']);

                if ($types->isNotEmpty()) {
                    $offset = 10000;

                    // Pass 1: Offset ilə müvəqqəti ID
                    foreach ($types as $t) {
                        $oldId = $t->id;
                        $tempId = (int)$t->portal_worker_type_id + $offset;

                        DB::table('employee_worker_types')->where('worker_type_id', $oldId)->update(['worker_type_id' => $tempId]);
                        DB::table('worker_type_bonus_rules')->where('worker_type_id', $oldId)->update(['worker_type_id' => $tempId]);
                        DB::table('worker_types')->where('id', $oldId)->update(['id' => $tempId]);
                    }

                    // Pass 2: Dəqiq portal_worker_type_id
                    foreach ($types as $t) {
                        $tempId = (int)$t->portal_worker_type_id + $offset;
                        $finalId = (int)$t->portal_worker_type_id;

                        DB::table('employee_worker_types')->where('worker_type_id', $tempId)->update(['worker_type_id' => $finalId]);
                        DB::table('worker_type_bonus_rules')->where('worker_type_id', $tempId)->update(['worker_type_id' => $finalId]);
                        DB::table('worker_types')->where('id', $tempId)->update(['id' => $finalId]);
                    }
                }

                if (DB::getDriverName() === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                }
            });

            if (DB::getDriverName() === 'mysql') {
                $maxId = (int)DB::table('worker_types')->max('id');
                $nextAuto = max($maxId + 1, 100);
                DB::statement("ALTER TABLE worker_types AUTO_INCREMENT = {$nextAuto};");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employees', 'branch_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
