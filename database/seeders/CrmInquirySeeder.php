<?php

namespace Database\Seeders;

use App\Models\CrmInquiryStatistic;
use Illuminate\Database\Seeder;

class CrmInquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            [
                'year' => 2026,
                'month' => 3,
                'period_label' => 'Mart 2026',
                'cancellations' => 25,
                'discounts' => 84,
                'reservations' => 303,
                'location' => 41,
                'games' => 139,
                'working_hours' => 29,
                'complaints' => 4,
                'other' => 97,
                'total' => 722,
            ],
            [
                'year' => 2026,
                'month' => 4,
                'period_label' => 'Aprel 2026',
                'cancellations' => 30,
                'discounts' => 77,
                'reservations' => 300,
                'location' => 34,
                'games' => 141,
                'working_hours' => 32,
                'complaints' => 3,
                'other' => 84,
                'total' => 701,
            ],
            [
                'year' => 2026,
                'month' => 5,
                'period_label' => 'May 2026',
                'cancellations' => 28,
                'discounts' => 73,
                'reservations' => 295,
                'location' => 36,
                'games' => 136,
                'working_hours' => 30,
                'complaints' => 3,
                'other' => 89,
                'total' => 690,
            ],
            [
                'year' => 2026,
                'month' => 6,
                'period_label' => 'İyun 2026',
                'cancellations' => 24,
                'discounts' => 71,
                'reservations' => 288,
                'location' => 35,
                'games' => 125,
                'working_hours' => 28,
                'complaints' => 2,
                'other' => 81,
                'total' => 654,
            ],
            [
                'year' => 2026,
                'month' => 7,
                'period_label' => 'İyul 2026',
                'cancellations' => 22,
                'discounts' => 66,
                'reservations' => 268,
                'location' => 32,
                'games' => 117,
                'working_hours' => 24,
                'complaints' => 3,
                'other' => 76,
                'total' => 608,
            ],
            [
                'year' => 2026,
                'month' => 8,
                'period_label' => 'Avgust 2026',
                'cancellations' => 26,
                'discounts' => 69,
                'reservations' => 273,
                'location' => 34,
                'games' => 126,
                'working_hours' => 27,
                'complaints' => 2,
                'other' => 82,
                'total' => 639,
            ],
        ];

        foreach ($rows as $data) {
            CrmInquiryStatistic::updateOrCreate(
                [
                    'year' => $data['year'],
                    'month' => $data['month'],
                ],
                $data
            );
        }
    }
}
