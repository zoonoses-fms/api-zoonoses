<?php

namespace App\Exports;

use App\Models\CampaignCycle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel;
use App\Models\ProfileWorker;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use stdClass;

class CampaignCyclePayrollExport implements FromCollection, Responsable
{
    use Exportable;

    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $today = new DateTime();
        $cycle = CampaignCycle::select(
            'id',
            'number',
            'description',
            'start',
            'campaign_id'
        )->with([
                'supports' => function ($query) {
                    $query->select('id', 'campaign_cycle_id', 'is_rural');
                },
                'supports.points' => function ($query) {
                    $query->select('id', 'campaign_support_id');
                }
            ])->findOrFail($this->id);

        $profiles = ProfileWorker::where('is_pre_load', true)->get();
        $idsPreload = [];


        foreach ($profiles as $profile) {
            $idsPreload[] = $profile->id;
        }

        $profile = $cycle->profiles()->orderBy('is_pre_campaign', 'desc')->first();
        $lastDate = new DateTime($cycle->start);
        $dates[] = $lastDate->format('d/m/Y');

        for ($i=1; $i <= $profile->is_pre_campaign; $i++) {
            $lastDate->sub(new DateInterval('P1D'));
            $dates[$i] = $lastDate->format('d/m/Y');
        }

        $listPayroll = [];

        for ($i=count($dates); $i >= 0; $i--) {
            $list = DB::table('campaign_worker')
            ->join('vaccination_workers', 'campaign_worker.vaccination_worker_id', '=', 'vaccination_workers.id')
            ->join('profile_workers', 'campaign_worker.profile_workers_id', '=', 'profile_workers.id')
            ->join('campaign_profile_workers', 'campaign_worker.profile_workers_id', '=', 'campaign_profile_workers.profile_workers_id')
            ->select(
                'vaccination_workers.registration as registration',
                'vaccination_workers.name as name',
                'profile_workers.name as profile',
                'profile_workers.id as profile_id',
                'campaign_profile_workers.cost as cost',
                'campaign_worker.is_pre_campaign'
            )->where('campaign_worker.campaign_cycle_id', $cycle->id)
            ->where('campaign_profile_workers.campaign_id', $cycle->campaign->id)
            ->where('campaign_worker.is_pre_campaign', $i)
            ->whereNotIn('campaign_worker.profile_workers_id', $idsPreload)
            ->orderBy('vaccination_workers.name', 'asc')
            ->get();

            foreach ($list as $item) {
                $listPayroll[] = [
                    $dates[$i],
                    $item->registration,
                    $item->name,
                    $item->profile,
                    $item->cost
                ];
            }
        }


        return new Collection($listPayroll);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ['date', 'registration', 'name', 'occupation', 'value'];
    }
}
