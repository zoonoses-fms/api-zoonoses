<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \stdClass;

class CampaignCycle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'campaign_cycles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'campaign_id',
        'number',
        'description',
        'start',
        'end'
    ];

    public function campaing()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function supports()
    {
        return $this->hasMany(CampaingSupport::class, 'campaign_cycle_id')->orderBy('order', 'asc');
    }

    public function statisticCoordinator()
    {
        return $this->belongsTo(VaccinationWorker::class, 'statistic_coordinator_id')->orderBy('name', 'asc');
    }

    public function statistics()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'statistic_cycle',
            'campaign_cycle_id',
            'statistic_id'
        );
    }

    public function coldChainCoordinator()
    {
        return $this->belongsTo(VaccinationWorker::class, 'cold_chain_coordinator_id')->orderBy('name', 'asc');
    }

    public function coldChainNurse()
    {
        return $this->belongsTo(VaccinationWorker::class, 'cold_chain_nurse_id')->orderBy('name', 'asc');
    }

    public function beforeColdChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'before_cold_chain_cycle',
            'campaign_cycle_id',
            'before_cold_chain_id'
        )->orderBy('name', 'asc');
    }

    public function startColdChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'start_cold_chain_cycle',
            'campaign_cycle_id',
            'start_cold_chain_id'
        )->orderBy('name', 'asc');
    }

    public function beforeDriverColdChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'before_driver_cold_chain_cycle',
            'campaign_cycle_id',
            'before_driver_cold_chain_id'
        )->orderBy('name', 'asc');
    }

    public function startDriverColdChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'start_driver_cold_chain_cycle',
            'campaign_cycle_id',
            'start_driver_cold_chain_id'
        )->orderBy('name', 'asc');
    }

    public function beforeTransports()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'before_transport_cycle',
            'campaign_cycle_id',
            'before_transport_id'
        )->orderBy('name', 'asc');
    }

    public function startTransports()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'start_transport_cycle',
            'campaign_cycle_id',
            'start_transport_id'
        )->orderBy('name', 'asc');
    }

    public function beforeZoonoses()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'before_zoonoses_cycle',
            'campaign_cycle_id',
            'before_zoonoses_id'
        )->orderBy('name', 'asc');
    }

    public function startZoonoses()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'start_zoonoses_cycle',
            'campaign_cycle_id',
            'start_zoonoses_id'
        )->orderBy('name', 'asc');
    }

    public function getTotalBeforePayRoll($counts)
    {
        $before['total'] = 0;
        $before['cold_chain_coordinator'] = 0;
        $before['cold_chain_nurse'] = 0;
        $before['cold_chain'] = 0;
        $before['driver_cold_chain'] = 0;
        $before['transport'] = 0;
        $before['zoonose'] = 0;


        if (!is_null($this->coldChainCoordinator)) {
            if ($this->partial_value && $this->campaing->cold_chain_coordinator_cost > 0) {
                $partial_cold_chain_coordinator_cost =
                    ($this->campaing->cold_chain_coordinator_cost / 100) * $this->percentage_value;

                $before['cold_chain_coordinator'] += $partial_cold_chain_coordinator_cost;
            } else {
                $before['cold_chain_coordinator'] += $this->campaing->cold_chain_coordinator_cost;
            }
            $before['total'] += $before['cold_chain_coordinator'];
            $counts['cold_chain_coordinator'] += 1;
        }

        if (!is_null($this->coldChainNurse)) {
            if ($this->partial_value && $this->campaing->cold_chain_nurse_cost > 0) {
                $partial_cold_chain_nurse_cost =
                    ($this->campaing->cold_chain_nurse_cost / 100) * $this->percentage_value;

                $before['cold_chain_nurse'] += $partial_cold_chain_nurse_cost;
            } else {
                $before['cold_chain_nurse'] += $this->campaing->cold_chain_nurse_cost;
            }

            $before['total'] += $before['cold_chain_nurse'];
            $counts['cold_chain_nurse'] += 1;
        }

        if ($this->partial_value && $this->campaing->cold_chain_cost > 0) {
            $partial_cold_chain_cost =
                ($this->campaing->cold_chain_cost / 100) * $this->percentage_value;

            $before['cold_chain'] += ($partial_cold_chain_cost * count($this->beforeColdChains));
        } else {
            $before['cold_chain'] += ($this->campaing->cold_chain_cost * count($this->beforeColdChains));
        }

        $before['total' ] += $before['cold_chain'];
        $counts['cold_chain'] += count($this->beforeColdChains);

        if ($this->partial_value && $this->campaing->driver_cost > 0) {
            $partial_driver_cost =
                ($this->campaing->driver_cost / 100) * $this->percentage_value;

            $before['driver_cold_chain'] += ($partial_driver_cost * count($this->beforeDriverColdChains));
        } else {
            $before['driver_cold_chain'] += ($this->campaing->driver_cost * count($this->beforeDriverColdChains));
        }

        $before['total'] += $before['driver_cold_chain'];
        $counts['driver_cold_chain'] += count($this->beforeDriverColdChains);

        if ($this->partial_value && $this->campaing->transport_cost > 0) {
            $partial_transport_cost =
                ($this->campaing->transport_cost / 100) * $this->percentage_value;

            $before['transport'] += ($partial_transport_cost * count($this->beforeTransports));
        } else {
            $before['transport'] += ($this->campaing->transport_cost * count($this->beforeTransports));
        }

        $before['total'] += $before['transport'];
        $counts['transport'] += count($this->beforeTransports);

        if ($this->partial_value && $this->campaing->zoonoses_cost > 0) {
            $partial_zoonoses_cost =
                ($this->campaing->zoonoses_cost / 100) * $this->percentage_value;

            $before['zoonose'] += ($partial_zoonoses_cost * count($this->beforeZoonoses));
        } else {
            $before['zoonose'] += ($this->campaing->zoonoses_cost * count($this->beforeZoonoses));
        }

        $before['total'] += $before['zoonose'];
        $counts[ 'zoonose'] += count($this->beforeZoonoses);
        return ['before' => $before, 'count' => $counts];
    }

    public function getTotalStartPayRoll($counts)
    {
        $start['total'] = 0;
        $start['cold_chain_coordinator'] = 0;
        $start['cold_chain_nurse'] = 0;
        $start['cold_chain'] = 0;
        $start['driver_cold_chain'] = 0;
        $start['transport'] = 0;
        $start['zoonose'] = 0;
        $start['statistic_coordinator'] = 0;
        $start['statistic'] = 0;
        $start['vaccinator'] = 0;
        $start['annotator'] = 0;
        $start['rural_supervisor'] = 0;
        $start['rural_assistant'] = 0;
        $start['coordinator'] = 0;
        $start['supervisor'] = 0;
        $start['assistant'] = 0;
        $start['driver'] = 0;

        if (!is_null($this->coldChainCoordinator)) {
            $start['cold_chain_coordinator'] += $this->campaing->cold_chain_coordinator_cost;
            $start['total'] += $start['cold_chain_coordinator'];
            $counts['cold_chain_coordinator'] += 1;
        }

        if (!is_null($this->coldChainNurse)) {
            $start['cold_chain_nurse'] += $this->campaing->cold_chain_nurse_cost;
            $start['total'] += $start['cold_chain_nurse'];
            $counts['cold_chain_nurse'] += 1;
        }

        $start['cold_chain'] += ($this->campaing->cold_chain_cost * count($this->startColdChains));
        $start['total'] += $start['cold_chain'];
        $counts['cold_chain'] += count($this->startColdChains);

        $start['driver_cold_chain'] += ($this->campaing->driver_cost * count($this->startDriverColdChains));
        $start['total'] += $start['driver_cold_chain'];
        $counts['driver_cold_chain'] += count($this->startDriverColdChains);

        $start['transport'] += ($this->campaing->transport_cost * count($this->startTransports));
        $start['total'] += $start['transport'];
        $counts['transport'] += count($this->startTransports);

        $start['zoonose'] += ($this->campaing->zoonoses_cost * count($this->startZoonoses));
        $start['total'] += $start['zoonose'];
        $counts['zoonose'] += count($this->startZoonoses);

        $start['statistic_coordinator'] += $this->campaing->statistic_coordinator_cost;
        $start['total'] += $start['statistic_coordinator'];
        $counts['statistic_coordinator'] += 1;

        $start['statistic'] += ($this->campaing->statisic_cost * count($this->statistics));
        $start['total'] += $start['statistic'];
        $counts['statistic'] += count($this->statistics);

        $registrationRuralSupervisors = [];
        $registrationRuralAssistants = [];

        foreach ($this->supports as $support) {
            $start['vaccinator'] += ($this->campaing->vaccinator_cost * count($support->vaccinators));

            if ($support->is_rural) {
                for ($i = 0; $i < count($support->ruralSupervisors); $i++) {
                    if (!in_array($support->ruralSupervisors[$i]->registration, $registrationRuralSupervisors)) {
                        $start['rural_supervisor'] += $this->campaing->rural_supervisor_cost;
                        $registrationRuralSupervisors[] = $support->ruralSupervisors[$i]->registration;
                        $counts['rural_supervisor'] += 1;
                    } else {
                        unset($support->ruralSupervisors[$i]);
                    }
                }

                for ($i = 0; $i < count($support->ruralAssistants); $i++) {
                    if (!in_array($support->ruralAssistants[$i]->registration, $registrationRuralAssistants)) {
                        $start['rural_assistant'] += $this->campaing->rural_assistant_cost;
                        $registrationRuralAssistants[] = $support->ruralAssistants[$i]->registration;
                        $counts['rural_assistant'] += 1;
                    } else {
                        unset($support->ruralAssistants[$i]);
                    }
                }
            } else {
                if (!is_null($support->coordinator)) {
                    $start['coordinator'] += $this->campaing->coordinator_cost;
                    $counts['coordinator'] += 1;
                }

                $start['supervisor'] += ($this->campaing->supervisor_cost * count($support->supervisors));
                $start['assistant'] += ($this->campaing->assistant_cost * count($support->assistants));
                $start['driver'] += ($this->campaing->driver_cost * count($support->drivers));
                $start['vaccinator'] += ($this->campaing->vaccinator_cost * count($support->vaccinators));

                $counts['supervisor'] += count($support->supervisors);
                $counts['assistant'] += count($support->assistants);
                $counts['driver'] += count($support->drivers);
                $counts['vaccinator'] += count($support->vaccinators);
            }


            foreach ($support->points as $point) {
                $start['vaccinator'] += ($this->campaing->vaccinator_cost * count($point->vaccinators));
                $start['annotator'] += ($this->campaing->annotators_cost * count($point->annotators));

                $counts['vaccinator'] += count($point->vaccinators);
                $counts['annotator'] += count($point->annotators);
            }
        }

        $start['total'] += $start['rural_supervisor'];
        $start['total'] += $start['rural_assistant'];

        $start['total'] += $start['coordinator'];
        $start['total'] += $start['supervisor'];
        $start['total'] += $start['assistant'];
        $start['total'] += $start['driver'];

        $start['total'] += $start['vaccinator'];
        $start['total'] += $start['annotator'];

        return ['start' => $start, 'count' => $counts];
    }

    public function getPayRoll()
    {
        $counts['cold_chain_coordinator'] = 0;
        $counts['cold_chain_nurse'] = 0;
        $counts['cold_chain'] = 0;
        $counts['driver_cold_chain'] = 0;
        $counts['transport'] = 0;
        $counts['zoonose'] = 0;
        $counts['statistic_coordinator'] = 0;
        $counts['statistic'] = 0;
        $counts['vaccinator'] = 0;
        $counts['annotator'] = 0;
        $counts['rural_supervisor'] = 0;
        $counts['rural_assistant'] = 0;
        $counts['coordinator'] = 0;
        $counts['supervisor'] = 0;
        $counts['assistant'] = 0;
        $counts['driver'] = 0;

        $before = $this->getTotalBeforePayRoll($counts);

        $total['before'] = $before['before'];

        $counts = $before['count'];

        $start = $this->getTotalStartPayRoll($counts);

        $total['start'] = $start['start'];

        $counts = $start['count'];

        $total['cycle']['total'] = 0;

        $total['cycle']['total'] += $total['before']['total'];

        $total['cycle']['total'] += $total['start']['total'];

        $total['cycle']['cold_chain_coordinator']
            = $total['before']['cold_chain_coordinator'] + $total['start']['cold_chain_coordinator'];

        $total['cycle']['cold_chain_nurse']
            = $total['before']['cold_chain_nurse'] + $total['start']['cold_chain_nurse'];

        $total['cycle']['cold_chain']
            = $total['before']['cold_chain'] + $total['start']['cold_chain'];

        $total['cycle']['driver_cold_chain']
            = $total['before']['driver_cold_chain'] + $total['start']['driver_cold_chain'];

        $total['cycle']['transport']
            = $total['before']['transport'] + $total['start']['transport'];

        $total['cycle']['zoonose']
            = $total['before']['zoonose'] + $total['start']['zoonose'];

        $total['cycle']['statistic_coordinator'] = $total['start']['statistic_coordinator'];

        $total['cycle']['statistic'] = $total['start']['statistic'];

        $total['cycle']['vaccinator'] = $total['start']['vaccinator'];
        $total['cycle']['annotator'] = $total['start']['annotator'];
        $total['cycle']['rural_supervisor'] =  $total['start']['rural_supervisor'];
        $total['cycle']['rural_assistant'] = $total['start']['rural_assistant'];
        $total['cycle']['coordinator'] = $total['start']['coordinator'];
        $total['cycle']['supervisor'] = $total['start']['supervisor'];
        $total['cycle']['assistant'] = $total['start']['assistant'];
        $total['cycle']['driver'] = $total['start']['driver'];

        return ['total' => $total, 'count' => $counts];
    }

    public static function buildItem($item)
    {
        $item->male_dog_under_4m = 0;
        $item->female_dog_under_4m = 0;

        $item->male_dog_major_4m_under_1y = 0;
        $item->female_dog_major_4m_under_1y = 0;

        $item->male_dog_major_1y_under_2y = 0;
        $item->female_dog_major_1y_under_2y = 0;

        $item->male_dog_major_2y_under_4y = 0;
        $item->female_dog_major_2y_under_4y = 0;

        $item->male_dog_major_4y = 0;
        $item->female_dog_major_4y = 0;

        $item->male_dogs = 0;
        $item->female_dogs = 0;

        $item->total_of_dogs = 0;

        $item->male_cat = 0;
        $item->female_cat = 0;

        $item->total_of_cats = 0;
        $item->total = 0;
        $item->goal = 0;
    }

    public static function incrementItem($item, $increment) {
        $item->male_dog_under_4m += $increment->male_dog_under_4m;
        $item->female_dog_under_4m += $increment->female_dog_under_4m;

        $item->male_dog_major_4m_under_1y += $increment->male_dog_major_4m_under_1y;
        $item->female_dog_major_4m_under_1y += $increment->female_dog_major_4m_under_1y;

        $item->male_dog_major_1y_under_2y += $increment->male_dog_major_1y_under_2y;
        $item->female_dog_major_1y_under_2y += $increment->female_dog_major_1y_under_2y;

        $item->male_dog_major_2y_under_4y += $increment->male_dog_major_2y_under_4y;
        $item->female_dog_major_2y_under_4y += $increment->female_dog_major_2y_under_4y;

        $item->male_dog_major_4y += $increment->male_dog_major_4y;
        $item->female_dog_major_4y += $increment->female_dog_major_4y;

        $item->male_dogs += $increment->male_dogs;
        $item->female_dogs += $increment->female_dogs;

        $item->total_of_dogs += $increment->total_of_dogs;

        $item->male_cat += $increment->male_cat;
        $item->female_cat += $increment->female_cat;

        $item->total_of_cats += $increment->total_of_cats;
        $item->total += $increment->total;
        $item->goal += $increment->goal;
    }

    public function loadReport()
    {
        $arraySaad = [];

        CampaignCycle::buildItem($this);

        foreach ($this->supports as $support) {
            CampaignCycle::buildItem($support);

            $saad_id = null;

            foreach ($arraySaad as $saad) {
                if ($support->saads[0]->id == $saad->id) {
                    $saad_id = $saad->id;
                    break;
                }
            }

            if ($saad_id == null) {
                $saad = new stdClass();
                $saad->id = $support->saads[0]->id;
                $saad->name = $support->saads[0]->name;
                $saad_id = $support->saads[0]->id;

                $arraySaad[$saad_id] = $saad;

                CampaignCycle::buildItem($arraySaad[$saad_id]);
            }

            foreach ($support->points as $point) {

                CampaignCycle::incrementItem($support, $point);
            }

            CampaignCycle::incrementItem($arraySaad[$saad_id], $support);

            CampaignCycle::incrementItem($this, $support);

        }
        $this->saads = $arraySaad;
    }
}
