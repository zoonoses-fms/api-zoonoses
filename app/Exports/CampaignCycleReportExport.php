<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\CampaignCycle;

class CampaignCycleReportExport implements FromView
{
    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
    public function view(): View
    {

        $today = date('d-m-Y');
        $arraySaad = [];
        $cycle = CampaignCycle::with([
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.saads',
            'supports.points.point',
        ])->findOrFail($this->id);

        $cycle->loadReport();
        return view('ncrlo.cycle_report', [
            'cycle' => $cycle,
            'today' => $today,
        ]);


    }
}
