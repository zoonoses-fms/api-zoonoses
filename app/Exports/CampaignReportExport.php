<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Campaign;

class CampaignReportExport implements FromView
{
    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
    public function view(): View
    {

        $today = date("d-m-Y");
        $arraySaad = [];
        $campaign = Campaign::findOrFail($this->id);

        Campaign::buildItem($campaign);
        foreach ($campaign->cycles as $cycle) {
            $cycle->loadReport();
            Campaign::incrementItem($campaign, $cycle);
        }

        return view(
            'ncrlo.campaign_report',
            [
                'campaign' => $campaign,
                'today' => $today,
            ]
        );

    }
}
