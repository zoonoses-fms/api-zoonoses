<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProfileWorker;
use App\Models\Campaign;
use App\Models\VaccinationWorker;

class MigrateAlocationWorkersSeender extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campaigns = Campaign::get();

        $coordinator_profile = ProfileWorker::where('name', 'Coordenador da Campanha')
            ->where('scope', 'campaign')->first();

        $cold_chain_coordinator_profile = ProfileWorker::where('name', 'Coordenador de Material')
            ->where('scope', 'cycle')->first();

        $cold_chain_nurse_profile = ProfileWorker::where('name', 'Enfermeiro')
            ->where('scope', 'cycle')->first();

        $cold_chains_profile  = ProfileWorker::where('name', 'Equipe de Apoio')
        ->where('scope', 'cycle')->where('management', 'Rede de Frio')->first();

        $zoonoes_profile  = ProfileWorker::where('name', 'Equipe de Apoio')
        ->where('scope', 'cycle')->where('management', 'GEZOON')->first();

        $transports_profile  = ProfileWorker::where('name', 'Equipe de Apoio')
        ->where('scope', 'cycle')->where('management', 'GETRANS')->first();

        $driver_cycle_profile = ProfileWorker::where('name', 'Motorista')
        ->where('scope', 'cycle')->first();

        $statistic_coordinator_profile = ProfileWorker::where('name', 'Coordenador de Estatística')
        ->where('scope', 'cycle')->first();

        $statistic_team_profile = ProfileWorker::where('name', 'Equipe de Estatística')
        ->where('scope', 'cycle')->first();

        $coordinator_of_pa_profile = ProfileWorker::where('name', 'Coordenador de PA')
        ->where('scope', 'support')->first();

        $supervisors_of_pa_profile = ProfileWorker::where('name', 'Supervisor de PA')
        ->where('scope', 'support')->first();

        $assistants_of_pa_profile = ProfileWorker::where('name', 'Equipe de Apoio de PA')
        ->where('scope', 'support')->first();

        $drivers_of_pa_profile = ProfileWorker::where('name', 'Motorista de PA')
        ->where('scope', 'support')->where('is_rural', false)->first();

        $drivers_of_rural_profile = ProfileWorker::where('name', 'Motorista Rural')
        ->where('scope', 'support')->where('is_rural', true)->first();

        $vaccinators_of_pa_profile = ProfileWorker::where('name', 'Vacinador Reserva')
        ->where('scope', 'support')->where('is_rural', false)->first();

        $vaccinators_of_rural_profile = ProfileWorker::where('name', 'Vacinador Rural')
        ->where('scope', 'support')->where('is_rural', true)->first();

        $supervisors_of_rural_profile = ProfileWorker::where('name', 'Supervisores Rural')
        ->where('scope', 'support')->where('is_rural', true)->first();

        $assistants_of_rural_profile = ProfileWorker::where('name', 'Auxiliar Rural')
        ->where('scope', 'support')->where('is_rural', true)->first();

        $supervisor_of_point_profile = ProfileWorker::where('name', 'Supervisor de Posto')
        ->where('scope', 'point')->first();

        $vaccinators_of_point_profile = ProfileWorker::where('name', 'Vacinador de Posto')
        ->where('scope', 'point')->first();

        $annotators_of_point_profile = ProfileWorker::where('name', 'Anotador de Posto')
        ->where('scope', 'point')->first();

        //Equipe de Estatística

        foreach ($campaigns as $campaign) {
            $coordinator_workers = [];

            if (!empty($campaign->coordinator_id)) {
                $coordinator_workers[$campaign->coordinator_id] = [
                    'campaign_id' => $campaign->id,
                    'campaign_cycle_id' => null,
                    'campaign_support_id' => null,
                    'campaign_point_id' => null,
                    'is_pre_campaign' => 0,
                    'is_single_allocation' => $coordinator_profile->is_single_allocation,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $coordinator_profile->workersAll()
                    ->wherePivot('campaign_id', $campaign->id)
                    ->wherePivotNull('campaign_cycle_id')
                    ->wherePivotNull('campaign_support_id')
                    ->wherePivotNull('campaign_point_id')
                    ->wherePivot('is_pre_campaign', 0)
                    ->sync($coordinator_workers);
            }

            foreach ($campaign->cycles as $cycle) {
                //pre_campaign true

                $cold_chain_coordinator_workers_pre = [];

                if (!empty($cycle->cold_chain_coordinator_id)) {
                    $cold_chain_coordinator_workers_pre[$cycle->cold_chain_coordinator_id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 1,
                        'is_single_allocation' => $cold_chain_coordinator_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $cold_chain_coordinator_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 1)
                        ->sync($cold_chain_coordinator_workers_pre);
                }

                $cold_chain_coordinator_workers = [];
                if (!empty($cycle->cold_chain_coordinator_id)) {
                    $cold_chain_coordinator_workers[$cycle->cold_chain_coordinator_id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 0,
                        'is_single_allocation' => $cold_chain_coordinator_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $cold_chain_coordinator_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($cold_chain_coordinator_workers);
                }

                //pre_campaign true

                $cold_chain_nurse_workers_pre = [];

                if (!empty($cycle->cold_chain_nurse_id)) {
                    $cold_chain_nurse_workers_pre[$cycle->cold_chain_nurse_id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 1,
                        'is_single_allocation' => $cold_chain_nurse_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $cold_chain_nurse_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 1)
                        ->sync($cold_chain_nurse_workers_pre);
                }

                $cold_chain_nurse_workers = [];
                if (!empty($cycle->cold_chain_nurse_id)) {
                    $cold_chain_nurse_workers[$cycle->cold_chain_nurse_id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 0,
                        'is_single_allocation' => $cold_chain_nurse_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $cold_chain_nurse_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($cold_chain_nurse_workers);
                }

                //pre_campaign true

                $cold_chains_profile_workers_pre = [];

                foreach ($cycle->beforeColdChains as $coldChain) {
                    $cold_chains_profile_workers_pre[$coldChain->id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 1,
                        'is_single_allocation' => $cold_chains_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $cold_chains_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 1)
                        ->sync($cold_chains_profile_workers_pre);

                $cold_chains_profile_workers = [];

                foreach ($cycle->startColdChains as $coldChain) {
                    $cold_chains_profile_workers[$coldChain->id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 0,
                        'is_single_allocation' => $cold_chains_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $cold_chains_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($cold_chains_profile_workers);

                //beforeZoonoses
                //pre_campaign true
                /*-------------------------------
                * Equipe de apoio da Zoonoses
                *-------------------------------_ */

                $zoonose_profile_workers_pre = [];

                foreach ($cycle->beforeZoonoses as $zoonose) {
                    $zoonose_profile_workers_pre[$zoonose->id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 1,
                        'is_single_allocation' => $zoonoes_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $zoonoes_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 1)
                        ->sync($zoonose_profile_workers_pre);

                $zoonose_profile_workers = [];

                foreach ($cycle->startZoonoses as $zoonose) {
                    $zoonose_profile_workers[$zoonose->id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 0,
                        'is_single_allocation' => $zoonoes_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $zoonoes_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($zoonose_profile_workers);


                //beforeTransports
                //pre_campaign true
                /*-------------------------------
                * Equipe de apoio da GETRANS
                *-------------------------------_ */
                $transport_profile_workers_pre = [];

                foreach ($cycle->beforeTransports as $transport) {
                    $transport_profile_workers_pre[$transport->id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 1,
                        'is_single_allocation' => $transports_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $transports_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 1)
                        ->sync($transport_profile_workers_pre);

                $transport_profile_workers = [];

                foreach ($cycle->startTransports as $transport) {
                    $transport_profile_workers[$transport->id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 0,
                        'is_single_allocation' => $transports_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $transports_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($transport_profile_workers);


                /* --------------------------------
                 *  Motorista da Etapa
                 * -------------------------------- */

                $driver_cycle_profile_workers = [];

                foreach ($cycle->startDriverColdChains as $driver) {
                    $driver_cycle_profile_workers[$driver->id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 0,
                        'is_single_allocation' => $driver_cycle_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $driver_cycle_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($driver_cycle_profile_workers);


                $statistic_coordinator_workers = [];
                if (!empty($cycle->statistic_coordinator_id)) {
                    $statistic_coordinator_workers[$cycle->statistic_coordinator_id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 0,
                        'is_single_allocation' => $statistic_coordinator_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $statistic_coordinator_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($statistic_coordinator_workers);
                }

                // statistic_team_profile
                $statistic_team_profile_workers = [];
                foreach ($cycle->statistics as $statistic) {
                    $statistic_team_profile_workers[$statistic->id] = [
                        'campaign_id' => $campaign->id,
                        'campaign_cycle_id' => $cycle->id,
                        'campaign_support_id' => null,
                        'campaign_point_id' => null,
                        'is_pre_campaign' => 0,
                        'is_single_allocation' => $statistic_team_profile->is_single_allocation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $statistic_team_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivotNull('campaign_support_id')
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($statistic_team_profile_workers);


                /* --------------------------------
                 *  Load Team de PA
                 * --------------------------------*/

                foreach ($cycle->supports as $support) {
                    //Coordenador de PA
                    $coordinator_of_pa__workers = [];

                    if (!empty($support->coordinator_id)) {
                        $coordinator_of_pa__workers[$support->coordinator_id] = [
                            'campaign_id' => $campaign->id,
                            'campaign_cycle_id' => $cycle->id,
                            'campaign_support_id' => $support->id,
                            'campaign_point_id' => null,
                            'is_pre_campaign' => 0,
                            'is_single_allocation' => $coordinator_of_pa_profile->is_single_allocation,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        $coordinator_of_pa_profile->workersAll()
                            ->wherePivot('campaign_id', $campaign->id)
                            ->wherePivot('campaign_cycle_id', $cycle->id)
                            ->wherePivot('campaign_support_id', $support->id)
                            ->wherePivotNull('campaign_point_id')
                            ->wherePivot('is_pre_campaign', 0)
                            ->sync($coordinator_of_pa__workers);
                    }

                    // Supervisores de PA
                    $supervisors_of_pa_workers = [];
                    foreach ($support->supervisors as $supervisor) {
                        $supervisors_of_pa_workers[$supervisor->id] = [
                            'campaign_id' => $campaign->id,
                            'campaign_cycle_id' => $cycle->id,
                            'campaign_support_id' => $support->id,
                            'campaign_point_id' => null,
                            'is_pre_campaign' => 0,
                            'is_single_allocation' => $supervisors_of_pa_profile->is_single_allocation,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }

                    $supervisors_of_pa_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivot('campaign_support_id', $support->id)
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($supervisors_of_pa_workers);


                    // Apoio de PA
                    $assistants_of_pa_workers = [];

                    foreach ($support->assistants as $assistant) {
                        $assistants_of_pa_workers[$assistant->id] = [
                            'campaign_id' => $campaign->id,
                            'campaign_cycle_id' => $cycle->id,
                            'campaign_support_id' => $support->id,
                            'campaign_point_id' => null,
                            'is_pre_campaign' => 0,
                            'is_single_allocation' => $assistants_of_pa_profile->is_single_allocation,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }

                    $assistants_of_pa_profile->workersAll()
                        ->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('campaign_cycle_id', $cycle->id)
                        ->wherePivot('campaign_support_id', $support->id)
                        ->wherePivotNull('campaign_point_id')
                        ->wherePivot('is_pre_campaign', 0)
                        ->sync($assistants_of_pa_workers);

                    //Drivers of PA
                    // drivers_of_pa_profile

                    $drivers_of_pa_workers = [];
                    $drivers_of_rural_workers = [];

                    if (!$support->is_rural) {
                        foreach ($support->drivers as $driver) {
                            $drivers_of_pa_workers[$driver->id] = [
                                'campaign_id' => $campaign->id,
                                'campaign_cycle_id' => $cycle->id,
                                'campaign_support_id' => $support->id,
                                'campaign_point_id' => null,
                                'is_pre_campaign' => 0,
                                'is_single_allocation' => $drivers_of_pa_profile->is_single_allocation,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }

                        $drivers_of_pa_profile->workersAll()
                            ->wherePivot('campaign_id', $campaign->id)
                            ->wherePivot('campaign_cycle_id', $cycle->id)
                            ->wherePivot('campaign_support_id', $support->id)
                            ->wherePivotNull('campaign_point_id')
                            ->wherePivot('is_pre_campaign', 0)
                            ->sync($drivers_of_pa_workers);
                    } else {
                        foreach ($support->drivers as $driver) {
                            $drivers_of_rural_workers[$driver->id] = [
                                'campaign_id' => $campaign->id,
                                'campaign_cycle_id' => $cycle->id,
                                'campaign_support_id' => $support->id,
                                'campaign_point_id' => null,
                                'is_pre_campaign' => 0,
                                'is_single_allocation' => $drivers_of_rural_profile->is_single_allocation,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }

                        $drivers_of_rural_profile->workersAll()
                            ->wherePivot('campaign_id', $campaign->id)
                            ->wherePivot('campaign_cycle_id', $cycle->id)
                            ->wherePivot('campaign_support_id', $support->id)
                            ->wherePivotNull('campaign_point_id')
                            ->wherePivot('is_pre_campaign', 0)
                            ->sync($drivers_of_rural_workers);
                    }


                    // Vaccination PA and Rural

                    $vaccinators_of_pa_workers = [];
                    $vaccinators_of_rural_workers = [];


                    if (!$support->is_rural) {
                        foreach ($support->vaccinators as $vaccinator) {
                            $vaccinators_of_pa_workers[$vaccinator->id] = [
                                'campaign_id' => $campaign->id,
                                'campaign_cycle_id' => $cycle->id,
                                'campaign_support_id' => $support->id,
                                'campaign_point_id' => null,
                                'is_pre_campaign' => 0,
                                'is_single_allocation' => $vaccinators_of_pa_profile->is_single_allocation,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }

                        $vaccinators_of_pa_profile->workersAll()
                            ->wherePivot('campaign_id', $campaign->id)
                            ->wherePivot('campaign_cycle_id', $cycle->id)
                            ->wherePivot('campaign_support_id', $support->id)
                            ->wherePivotNull('campaign_point_id')
                            ->wherePivot('is_pre_campaign', 0)
                            ->sync($vaccinators_of_pa_workers);
                    } else {
                        foreach ($support->vaccinators as $vaccinator) {
                            $vaccinators_of_rural_workers[$vaccinator->id] = [
                                'campaign_id' => $campaign->id,
                                'campaign_cycle_id' => $cycle->id,
                                'campaign_support_id' => $support->id,
                                'campaign_point_id' => null,
                                'is_pre_campaign' => 0,
                                'is_single_allocation' => $vaccinators_of_rural_profile->is_single_allocation,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }

                        $vaccinators_of_rural_profile->workersAll()
                            ->wherePivot('campaign_id', $campaign->id)
                            ->wherePivot('campaign_cycle_id', $cycle->id)
                            ->wherePivot('campaign_support_id', $support->id)
                            ->wherePivotNull('campaign_point_id')
                            ->wherePivot('is_pre_campaign', 0)
                            ->sync($vaccinators_of_rural_workers);


                        $supervisors_of_rural_workers = [];

                        foreach ($support->ruralSupervisors as $supervisor) {
                            $supervisors_of_rural_workers[$supervisor->id] = [
                                'campaign_id' => $campaign->id,
                                'campaign_cycle_id' => $cycle->id,
                                'campaign_support_id' => $support->id,
                                'campaign_point_id' => null,
                                'is_pre_campaign' => 0,
                                'is_single_allocation' => $supervisors_of_rural_profile->is_single_allocation,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }

                        $supervisors_of_rural_profile->workersAll()
                            ->wherePivot('campaign_id', $campaign->id)
                            ->wherePivot('campaign_cycle_id', $cycle->id)
                            ->wherePivot('campaign_support_id', $support->id)
                            ->wherePivotNull('campaign_point_id')
                            ->wherePivot('is_pre_campaign', 0)
                            ->sync($supervisors_of_rural_workers);

                        // ruralAssistants

                        $assistants_of_rural_workers = [];

                        foreach ($support->ruralAssistants as $assistant) {
                            $assistants_of_rural_workers[$assistant->id] = [
                                'campaign_id' => $campaign->id,
                                'campaign_cycle_id' => $cycle->id,
                                'campaign_support_id' => $support->id,
                                'campaign_point_id' => null,
                                'is_pre_campaign' => 0,
                                'is_single_allocation' => $assistants_of_rural_profile->is_single_allocation,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }

                        $assistants_of_rural_profile->workersAll()
                            ->wherePivot('campaign_id', $campaign->id)
                            ->wherePivot('campaign_cycle_id', $cycle->id)
                            ->wherePivot('campaign_support_id', $support->id)
                            ->wherePivotNull('campaign_point_id')
                            ->wherePivot('is_pre_campaign', 0)
                            ->sync($assistants_of_rural_workers);
                    }

                    /*-------------------------------
                    * Load Equipe dos postos devacina
                    *
                    */
                    /*------------------------------
                    $supervisor_of_point_profile = ProfileWorker::where('name', 'Supervisor de Posto')
                    ->where('scope', 'point')->first();

                    $vaccinators_of_point_profile = ProfileWorker::where('name', 'Vacinador de Posto')
                    ->where('scope', 'point')->first();

                    $annotators_of_point_profile = ProfileWorker::where('name', 'Anotador de Posto')
                    ->where('scope', 'point')->first();
                    *-------------------------------*/
                    if (!$support->is_rural) {
                        foreach ($support->points as $point) {
                            $supervisor_of_point_workers = [];

                            if (!empty($point->supervisor_id)) {
                                $supervisor_of_point_workers[$point->supervisor_id] = [
                                    'campaign_id' => $campaign->id,
                                    'campaign_cycle_id' => $cycle->id,
                                    'campaign_support_id' => $support->id,
                                    'campaign_point_id' => $point->id,
                                    'is_pre_campaign' => 0,
                                    'is_single_allocation' => $supervisor_of_point_profile->is_single_allocation,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];

                                $supervisor_of_point_profile->workersAll()
                                    ->wherePivot('campaign_id', $campaign->id)
                                    ->wherePivot('campaign_cycle_id', $cycle->id)
                                    ->wherePivot('campaign_support_id', $support->id)
                                    ->wherePivot('campaign_point_id', $point->id)
                                    ->wherePivot('is_pre_campaign', 0)
                                    ->sync($supervisor_of_point_workers);
                            }

                            $vaccinators_of_point_workers = [];

                            foreach ($point->vaccinators as $vaccinator) {
                                $vaccinators_of_point_workers[$vaccinator->id] = [
                                    'campaign_id' => $campaign->id,
                                    'campaign_cycle_id' => $cycle->id,
                                    'campaign_support_id' => $support->id,
                                    'campaign_point_id' => $point->id,
                                    'is_pre_campaign' => 0,
                                    'is_single_allocation' => $vaccinators_of_point_profile->is_single_allocation,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                            }

                            $vaccinators_of_point_profile->workersAll()
                                ->wherePivot('campaign_id', $campaign->id)
                                ->wherePivot('campaign_cycle_id', $cycle->id)
                                ->wherePivot('campaign_support_id', $support->id)
                                ->wherePivot('campaign_point_id', $point->id)
                                ->wherePivot('is_pre_campaign', 0)
                                ->sync($vaccinators_of_point_workers);


                            $annotators_of_point_workers = [];

                            foreach ($point->annotators as $annotator) {
                                $annotators_of_point_workers[$annotator->id] = [
                                    'campaign_id' => $campaign->id,
                                    'campaign_cycle_id' => $cycle->id,
                                    'campaign_support_id' => $support->id,
                                    'campaign_point_id' => $point->id,
                                    'is_pre_campaign' => 0,
                                    'is_single_allocation' => $annotators_of_point_profile->is_single_allocation,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                            }

                            $annotators_of_point_profile->workersAll()
                                ->wherePivot('campaign_id', $campaign->id)
                                ->wherePivot('campaign_cycle_id', $cycle->id)
                                ->wherePivot('campaign_support_id', $support->id)
                                ->wherePivot('campaign_point_id', $point->id)
                                ->wherePivot('is_pre_campaign', 0)
                                ->sync($annotators_of_point_workers);
                        }
                    }
                }
            }
        }
    }
}
