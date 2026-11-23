<?php

namespace Database\Seeders;

use App\Services\WorkerTypeService;
use Illuminate\Database\Seeder;

class WorkerTypeSeeder extends Seeder
{
    /**
     * Standart işçi tiplərini və vizual bonus xal qaydalarını doldurur.
     */
    public function run(): void
    {
        app(WorkerTypeService::class)->seedDefaults();
    }
}

