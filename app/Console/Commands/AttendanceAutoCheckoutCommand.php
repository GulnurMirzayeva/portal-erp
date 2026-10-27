<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Command;

class AttendanceAutoCheckoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Çıxış etməyi unudan işçiləri təyin olunmuş saatda (məs: 06:00) avtomatik sistem çıxışı (logout) edir';

    /**
     * Execute the console command.
     */
    public function handle(AttendanceService $attendanceService): int
    {
        $this->info('Davamiyyət avtomatik çıxış prosesi yoxlanılır...');

        $closed = $attendanceService->performAutoCheckout();

        if ($closed > 0) {
            $this->info("Uğurla tamamlandı: {$closed} işçi üçün avtomatik sistem çıxışı qeydə alındı.");
        } else {
            $this->line('Avtomatik çıxış tələb edən açıq seans tapılmadı və ya funksionallıq deaktivdir.');
        }

        return Command::SUCCESS;
    }
}
