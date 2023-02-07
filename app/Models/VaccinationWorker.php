<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class VaccinationWorker extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vaccination_workers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'cpf',
        'registration'
    ];

    public function profiles()
    {
        return $this->belongsToMany(
            ProfileWorker::class,
            'campaign_worker',
            'vaccination_worker_id',
            'profile_workers_id'
        );
    }

    public function coordinations()
    {
        return $this->hasMany(CampaignSupport::class, 'vaccination_worker_id');
    }

    public function supervisions()
    {
        return $this->hasMany(CampaignPoint::class, 'vaccination_worker_id');
    }

    public function supports()
    {
        return $this->belongsToMany(
            CampaignSupport::class,
            'vaccination_worker_campaign_support',
            'vaccination_worker_id',
            'campaign_support_id'
        );
    }

    public function points()
    {
        return $this->belongsToMany(
            CampaignPoint::class,
            'campaign_worker',
            'vaccination_worker_id',
            'campaign_point_id',
        );
    }

    public static function listNotFreeWorkers($campaign_cycle_id)
    {
        $cycle = CampaignCycle::with('supports.points')->find($campaign_cycle_id);

        $listNotFree = [];

        foreach ($cycle->supports as $support) {
            foreach ($support->points as $point) {
                foreach ($point->vaccinators as $vaccinator) {
                    $listNotFree[] = $vaccinator->id;
                }

                foreach ($point->annotators as $annotator) {
                    $listNotFree[] = $annotator->id;
                }
            }
            if ($support->coordinator_id != null) {
                $listNotFree[] = $support->coordinator_id;
            }
            foreach ($support->supervisors as $supervisor) {
                $listNotFree[] = $supervisor->id;
            }
            foreach ($support->drivers as $driver) {
                $listNotFree[] = $driver->id;
            }
            /*
            foreach ($support->ruralSupervisors as $ruralSupervisor) {
                $listNotFree[] = $ruralSupervisor->id;
            }
            */
            foreach ($support->ruralAssistants as $ruralAssistant) {
                $listNotFree[] = $ruralAssistant->id;
            }
        }
        return array_unique($listNotFree);
    }

    public static function listFreeWorkers(Request $request, $listNotFreeWorkers)
    {
        return VaccinationWorker::when(
            $request->has('keyword'),
            function ($query) use ($request) {
                $keyword = $request->keyword;
                return $query->whereRaw(
                    "unaccent(name) ilike unaccent('%{$keyword}%')"
                )->orWhereRaw(
                    "unaccent(registration) ilike unaccent('%{$keyword}%')"
                );
            }
        )
        ->whereNotIn('id', $listNotFreeWorkers)
        ->orderBy('name', 'asc')
        ->get();
    }
}
