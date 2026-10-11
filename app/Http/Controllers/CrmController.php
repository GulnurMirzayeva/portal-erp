<?php

namespace App\Http\Controllers;

use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    /**
     * CRM əsas səhifəsi və statistika cədvəlləri.
     */
    public function index(Request $request, PortalWebsiteService $portalService)
    {
        $availableMonths = $this->getAvailableMonths();

        // Cari ay filtri
        $filterMonth = $request->query('month');
        if (empty($filterMonth) || !preg_match('/^\d{4}-\d{2}$/', $filterMonth)) {
            $filterMonth = Carbon::now()->format('Y-m');
        }

        $filterBranch = $request->query('branch_id');

        // Filiallar siyahısı ("Ofis" çıxarılır)
        $allBranches = $portalService->getBranches();
        $branches = array_values(array_filter($allBranches, function ($b) {
            $name = mb_strtolower($b['name'] ?? '');
            return !str_contains($name, 'ofis') && !str_contains($name, 'office');
        }));

        $selectedBranchName = 'Bütün Filiallar';
        if ($filterBranch && $filterBranch !== 'all') {
            foreach ($branches as $b) {
                if ($b['id'] == $filterBranch) {
                    $selectedBranchName = $b['name'];
                    break;
                }
            }
        }

        return view('crm.index', [
            'branches' => $branches,
            'availableMonths' => $availableMonths,
            'filterBranch' => $filterBranch,
            'filterMonth' => $filterMonth,
            'selectedBranchName' => $selectedBranchName,
        ]);
    }

    /**
     * Mövcud ayların siyahısı.
     */
    protected function getAvailableMonths(): array
    {
        $monthNamesAz = [
            '01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel',
            '05' => 'May', '06' => 'İyun', '07' => 'İyul', '08' => 'Avqust',
            '09' => 'Sentyabr', '10' => 'Oktyabr', '11' => 'Noyabr', '12' => 'Dekabr',
        ];

        $currentYm = Carbon::now()->format('Y-m');
        $minYm = '2025-11';
        $maxYm = $currentYm;

        $cur = Carbon::parse($minYm . '-01');
        $end = Carbon::parse($maxYm . '-01');
        $allMonths = collect();
        while ($cur->lessThanOrEqualTo($end)) {
            $allMonths->push($cur->format('Y-m'));
            $cur->addMonth();
        }

        $sorted = $allMonths->unique()->sort()->values();

        return $sorted->map(function ($ym) use ($monthNamesAz) {
            [$y, $m] = explode('-', $ym);
            return [
                'key' => $ym,
                'name' => ($monthNamesAz[$m] ?? $m) . ' ' . $y,
                'year' => $y,
                'month' => $m,
            ];
        })->all();
    }
}
