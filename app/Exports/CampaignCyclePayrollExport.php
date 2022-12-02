<?php

namespace App\Exports;

use App\Models\CampaignCycle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
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
        $cycle = CampaignCycle::with([
            'campaing',
            'coldChainCoordinator',
            'coldChainNurse',
            'beforeColdChains',
            'startColdChains',
            'beforeDriverColdChains',
            'startDriverColdChains',
            'statisticCoordinator',
            'statistics',
            'beforeTransports',
            'startTransports',
            'beforeZoonoses',
            'startZoonoses',
            'supports.coordinator',
            'supports.supervisors',
            'supports.assistants',
            'supports.drivers',
            'supports.vaccinators',
            'supports.ruralSupervisors',
            'supports.ruralAssistants',
            'supports.points.vaccinators',
            'supports.points.annotators',

        ])->findOrFail($this->id);

        // return $cycle;

        $start = new DateTime($cycle->start);
        $before = new DateTime($cycle->start);
        //Subtract a day using DateInterval
        $before->sub(new DateInterval('P1D'));

        //Get the date in a YYYY-MM-DD format.
        // $before = $before->format('d/m/Y');
        // $start = $start->format('d/m/Y');
        // $currentDate = $today->format('d/m/Y');
        // $today = $today->format('Y-m-d');

        $listPayroll = [];

        if ($cycle->coldChainCoordinator) {
            if ($cycle->partial_value && $cycle->campaing->cold_chain_coordinator_cost > 0) {
                $partial_cold_chain_coordinator_cost =
                    ($cycle->campaing->cold_chain_coordinator_cost / 100) * $cycle->percentage_value;

                $listPayroll[] = [
                    $before,
                    $cycle->coldChainCoordinator->registration,
                    $cycle->coldChainCoordinator->name,
                    'Coordenador da Rede de Frio',
                    $partial_cold_chain_coordinator_cost
                ];
            } else {
                $listPayroll[] = [
                    $before,
                    $cycle->coldChainCoordinator->registration,
                    $cycle->coldChainCoordinator->name,
                    'Coordenador da Rede de Frio',
                    $cycle->campaing->cold_chain_coordinator_cost
                ];
            }
        }

        if ($cycle->coldChainNurse) {
            if ($cycle->partial_value && $cycle->campaing->cold_chain_nurse_cost > 0) {
                $partial_cold_chain_nurse_cost =
                    ($cycle->campaing->cold_chain_nurse_cost / 100) * $cycle->percentage_value;

                $listPayroll[] = [
                    $before,
                    $cycle->coldChainNurse->registration,
                    $cycle->coldChainNurse->name,
                    'Enfermeira da Rede de Frio',
                    $partial_cold_chain_nurse_cost
                ];
            } else {
                $listPayroll[] = [
                    $before,
                    $cycle->coldChainNurse->registration,
                    $cycle->coldChainNurse->name,
                    'Enfermeira da Rede de Frio',
                    $cycle->campaing->cold_chain_nurse_cost
                ];
            }
        }

        if ($cycle->partial_value && $cycle->campaing->cold_chain_cost > 0) {
            $partial_cold_chain_cost =
                ($cycle->campaing->cold_chain_cost / 100) * $cycle->percentage_value;

            foreach ($cycle->beforeColdChains as $beforeColdChain) {
                $listPayroll[] = [
                    $before,
                    $beforeColdChain->registration,
                    $beforeColdChain->name,
                    'Equipe da Rede de Frio',
                    $partial_cold_chain_cost
                ];
            }
        } else {
            foreach ($cycle->beforeColdChains as $beforeColdChain) {
                $listPayroll[] = [
                    $before,
                    $beforeColdChain->registration,
                    $beforeColdChain->name,
                    'Equipe da Rede de Frio',
                    $cycle->campaing->cold_chain_cost
                ];
            }
        }

        if ($cycle->partial_value && $cycle->campaing->driver_cost > 0) {
            $partial_driver_cost =
                ($cycle->campaing->driver_cost / 100) * $cycle->percentage_value;

            foreach ($cycle->beforeDriverColdChains as $beforeDriverColdChain) {
                $listPayroll[] = [
                    $before,
                    $beforeDriverColdChain->registration,
                    $beforeDriverColdChain->name,
                    'Motorista',
                    $partial_driver_cost
                ];
            }
        } else {
            foreach ($cycle->beforeDriverColdChains as $beforeDriverColdChain) {
                $listPayroll[] = [
                    $before,
                    $beforeDriverColdChain->registration,
                    $beforeDriverColdChain->name,
                    'Motorista',
                    $cycle->campaing->driver_cost
                ];
            }
        }

        if ($cycle->partial_value && $cycle->campaing->transport_cost > 0) {
            $partial_transport_cost =
                ($cycle->campaing->transport_cost / 100) * $cycle->percentage_value;

            foreach ($cycle->beforeTransports as $beforeTransport) {
                $listPayroll[] = [
                    $before,
                    $beforeTransport->registration,
                    $beforeTransport->name,
                    'Apoio da GETRANS',
                    $partial_transport_cost
                ];
            }
        } else {
            foreach ($cycle->beforeTransports as $beforeTransport) {
                $listPayroll[] = [
                    $before,
                    $beforeTransport->registration,
                    $beforeTransport->name,
                    'Apoio da GETRANS',
                    $cycle->campaing->transport_cost
                ];
            }
        }

        if ($cycle->partial_value && $cycle->campaing->zoonoses_cost > 0) {
            $partial_zoonoses_cost =
                ($cycle->campaing->zoonoses_cost / 100) * $cycle->percentage_value;

            foreach ($cycle->beforeZoonoses as $beforeZoonose) {
                $listPayroll[] = [
                    $before,
                    $beforeZoonose->registration,
                    $beforeZoonose->name,
                    'Apoio da GEZOON',
                    $partial_zoonoses_cost
                ];
            }
        } else {
            foreach ($cycle->beforeZoonoses as $beforeZoonose) {
                $listPayroll[] = [
                    $before,
                    $beforeZoonose->registration,
                    $beforeZoonose->name,
                    'Apoio da GEZOON',
                    $cycle->campaing->zoonoses_cost
                ];
            }
        }


        // start day

        if ($cycle->coldChainCoordinator) {
            $listPayroll[] = [
                $start,
                $cycle->coldChainCoordinator->registration,
                $cycle->coldChainCoordinator->name,
                'Coordenador da Rede de Frio',
                $cycle->campaing->cold_chain_coordinator_cost
            ];
        }

        if ($cycle->coldChainNurse) {
            $listPayroll[] = [
                $start,
                $cycle->coldChainNurse->registration,
                $cycle->coldChainNurse->name,
                'Enfermeira da Rede de Frio',
                $cycle->campaing->cold_chain_nurse_cost
            ];
        }


        foreach ($cycle->startColdChains as $startColdChain) {
            $listPayroll[] = [
                $start,
                $startColdChain->registration,
                $startColdChain->name,
                'Equipe da Rede de Frio',
                $cycle->campaing->cold_chain_cost
            ];
        }

        foreach ($cycle->startDriverColdChains as $startDriverColdChain) {
            $listPayroll[] = [
                $start,
                $startDriverColdChain->registration,
                $startDriverColdChain->name,
                'Motorista da Rede de Frio',
                $cycle->campaing->driver_cost
            ];
        }

        foreach ($cycle->startTransports as $startTransport) {
            $listPayroll[] = [
                $start,
                $startTransport->registration,
                $startTransport->name,
                'Apoio da GETRANS',
                $cycle->campaing->transport_cost
            ];
        }

        foreach ($cycle->startZoonoses as $startZoonose) {
            $listPayroll[] = [
                $start,
                $startZoonose->registration,
                $startZoonose->name,
                'Apoio da GEZOON',
                $cycle->campaing->zoonoses_cost
            ];
        }

        if (!$cycle->supports[0]->is_rural) {
            foreach ($cycle->supports as $support) {
                if ($support->coordinator) {
                    $listPayroll[] = [
                        $start,
                        $support->coordinator->registration,
                        $support->coordinator->name,
                        'Coordenador',
                        $cycle->campaing->coordinator_cost
                    ];
                }
            }
        }

        foreach ($cycle->supports as $support) {
            if ($support->is_rural) {
                foreach ($support->ruralSupervisors as $ruralSupervisor) {
                    $listPayroll[] = [
                        $start,
                        $ruralSupervisor->registration,
                        $ruralSupervisor->name,
                        'Supervisor Rural',
                        $cycle->campaing->rural_supervisor_cost
                    ];
                }
            } else {
                foreach ($support->supervisors as $supervisor) {
                    $listPayroll[] = [
                        $start,
                        $supervisor->registration,
                        $supervisor->name,
                        'Supervisor',
                        $cycle->campaing->supervisor_cost
                    ];
                }
            }
        }

        foreach ($cycle->supports as $support) {
            if ($support->is_rural) {
                foreach ($support->ruralAssistants as $ruralAssistant) {
                    $listPayroll[] = [
                        $start,
                        $ruralAssistant->registration,
                        $ruralAssistant->name,
                        'Auxiliar Rural',
                        $cycle->campaing->rural_assistant_cost
                    ];
                }
            } else {
                foreach ($support->assistants as $assistant) {
                    $listPayroll[] = [
                        $start,
                        $assistant->registration,
                        $assistant->name,
                        'Auxiliar',
                        $cycle->campaing->assistant_cost
                    ];
                }
            }
        }

        foreach ($cycle->supports as $support) {
            foreach ($support->drivers as $driver) {
                $listPayroll[] = [
                    $start,
                    $driver->registration,
                    $driver->name,
                    'Motorista do Ponto de Apoio',
                    $cycle->campaing->driver_cost
                ];
            }
        }


        foreach ($cycle->supports as $support) {
            foreach ($support->vaccinators as $vaccinator) {
                $listPayroll[] = [
                    $start,
                    $vaccinator->registration,
                    $vaccinator->name,
                    'Vacinador',
                    $cycle->campaing->vaccinator_cost
                ];
            }

            foreach ($support->points as $point) {
                foreach ($point->vaccinators as $vaccinator) {
                    $listPayroll[] = [
                        $start,
                        $vaccinator->registration,
                        $vaccinator->name,
                        'Vacinador',
                        $cycle->campaing->vaccinator_cost
                    ];
                }
            }
        }

        if (!$cycle->supports[0]->is_rural) {
            foreach ($cycle->supports as $support) {
                foreach ($support->points as $point) {
                    foreach ($point->annotators as $annotator) {
                        $listPayroll[] = [
                            $start,
                            $annotator->registration,
                            $annotator->name,
                            'Anotador',
                            $cycle->campaing->annotators_cost
                        ];
                    }
                }
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
