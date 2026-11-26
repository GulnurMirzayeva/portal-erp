<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('attendance_workers')) {
            return;
        }

        // 1. portal_worker_id üzrə dublikatları təmizləyirik
        $dups = DB::table('attendance_workers')
            ->select('portal_worker_id')
            ->whereNotNull('portal_worker_id')
            ->groupBy('portal_worker_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('portal_worker_id');

        foreach ($dups as $pwId) {
            $workers = DB::table('attendance_workers')
                ->where('portal_worker_id', $pwId)
                ->orderBy('id')
                ->get();

            // Əgər cihazı bağlı olan varsa, onu saxla. Yoxdursa, logu olanı saxla. Yoxdursa, birincini saxla.
            $keep = $workers->first(fn($w) => !empty($w->registered_device_uuid));

            if (!$keep && Schema::hasTable('attendance_logs')) {
                $keep = $workers->first(fn($w) => DB::table('attendance_logs')->where('worker_id', $w->id)->exists());
            }

            if (!$keep) {
                $keep = $workers->first();
            }

            foreach ($workers as $w) {
                if ($w->id === $keep->id) {
                    continue;
                }

                // Əgər silinəcək qeydə bağlı loglar varsa, onları saxlanılan qeydə köçür
                if (Schema::hasTable('attendance_logs')) {
                    DB::table('attendance_logs')
                        ->where('worker_id', $w->id)
                        ->update(['worker_id' => $keep->id]);
                }

                // Əgər saxlanılanın cihazı yoxdursa, amma silinəndə varsa, cihazı qoru
                if (empty($keep->registered_device_uuid) && !empty($w->registered_device_uuid)) {
                    DB::table('attendance_workers')
                        ->where('id', $keep->id)
                        ->update(['registered_device_uuid' => $w->registered_device_uuid]);
                    $keep->registered_device_uuid = $w->registered_device_uuid;
                }

                DB::table('attendance_workers')->where('id', $w->id)->delete();
            }
        }

        // 2. portal_worker_id sütununa UNIQUE index əlavə edirik
        try {
            Schema::table('attendance_workers', function (Blueprint $table) {
                $table->unique('portal_worker_id', 'attendance_workers_portal_worker_id_unique');
            });
        } catch (\Exception $e) {
            // Index artıq mövcuddursa, davam et
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendance_workers')) {
            try {
                Schema::table('attendance_workers', function (Blueprint $table) {
                    $table->dropUnique('attendance_workers_portal_worker_id_unique');
                });
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }
};
