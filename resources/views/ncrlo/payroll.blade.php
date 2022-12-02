<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Folha de Pagamento</title>
    <style>
        .conteiner {
            margin: 0;
        }

        .date {
            margin: 0;
            width: 80px;
            margin-left: auto;
            margin-right: 0;
        }

        address {
            margin-left: auto;
            margin-right: auto;
            display: block;
            font-style: italic;
        }

        .header {
            width: 100%;
        }

        .center {
            width: 50%;
            margin-left: auto;
            margin-right: auto;
        }

        .signature {
            width: max-content;
            margin-left: auto;
            margin-right: auto;
        }

        .logo-header {
            display: inline-block;
        }

        .logo-text {
            margin-left: 20px;
            display: inline-block;
        }

        .logo-header img {
            width: 100px;
        }

        ol {
            margin-left: 0;
        }

        .flex-box {
            display: flex;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .border {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .name {
            width: 40%;
        }

        td {
            height: 30px;
        }

        td.line {
            border-bottom: 1px solid;
            width: 60%;
        }

        .line-vaccinator {
            width: 40%;
        }

        .line-mat {
            width: 5%;
        }

        .line-origin {
            width: 5%;
        }

        .line-name {
            width: 40%;
        }
    </style>
</head>

<body>
    <div class="conteiner">
        <div class="date">
            <strong>{{ $currentDate }}</strong>
        </div>
        <div class="header">
            <div class="logo-header">
                <img src="img/logo_teresina.jpg" alt="logo">
            </div>
            <div class="logo-text">
                <strong>Prefeitura Municipal de Teresina</strong><br />
                <strong>Fundação Municipal de Saúde</strong><br />
                <strong>Gerência de Zoonoses GEZOON</strong><br />
                <strong>Núcleo de Controle da Raiva, Leishmaniose e Outras Zoonoses - NCRLOZ</strong><br />
            </div>
            <div class="center" style="text-align:center">
                <h3>Folha de pagamento </h3>
            </div>
        </div>

        <div class="content">

            <div>
                <strong> {{ $cycle->description }} </strong>
                <strong>- Folha de pagamento de {{ $before }} </strong>
            </div>
            <table class="table-vacination">
                <thead>
                    <th class="border">Mat.</th>
                    <th class="border">Nome</th>
                    <th class="border">Funçao</th>
                    <th class="border">Valor</th>
                </thead>
                @if ($cycle->coldChainCoordinator)
                    <tr>
                        <td class="border line-mat"> {{ $cycle->coldChainCoordinator->registration }} </td>
                        <td class="border line-name"> {{ $cycle->coldChainCoordinator->name }} </td>
                        <td class="border"> Coordenador da Rede de Frio</td>
                        @if ($cycle->partial_value && $cycle->campaing->cold_chain_coordinator_cost > 0)
                            <td class="border"> R$
                                {{ number_format(($cycle->campaing->cold_chain_coordinator_cost / 100) * $cycle->percentage_value, 2, ',', ' ') }}
                            </td>
                        @else
                            <td class="border"> R$
                                {{ number_format($cycle->campaing->cold_chain_coordinator_cost, 2, ',', ' ') }}
                            </td>
                        @endif
                    </tr>
                @endif
                @if ($cycle->coldChainNurse)
                    <tr>
                        <td class="border line-mat"> {{ $cycle->coldChainNurse->registration }} </td>
                        <td class="border line-name"> {{ $cycle->coldChainNurse->name }} </td>
                        <td class="border"> Enfermeira da Rede de Frio</td>
                        @if ($cycle->partial_value && $cycle->campaing->cold_chain_nurse_cost > 0)
                            <td class="border"> R$
                                {{ number_format(($cycle->campaing->cold_chain_nurse_cost / 100) * $cycle->percentage_value, 2, ',', ' ') }}
                            </td>
                        @else
                            <td class="border">R$
                                {{ number_format($cycle->campaing->cold_chain_nurse_cost, 2, ',', ' ') }}
                            </td>
                        @endif

                    </tr>
                @endif

                <tr>
                    <td class="border" colspan="4"></td>
                </tr>
                @foreach ($cycle->beforeColdChains as $beforeColdChain)
                    <tr>
                        <td class="border line-mat"> {{ $beforeColdChain->registration }} </td>
                        <td class="border line-name"> {{ $beforeColdChain->name }} </td>
                        <td class="border"> Equipe da Rede de Frio </td>
                        @if ($cycle->partial_value && $cycle->campaing->cold_chain_cost > 0)
                            <td class="border"> R$
                                {{ number_format(($cycle->campaing->cold_chain_cost / 100) * $cycle->percentage_value, 2, ',', ' ') }}
                            </td>
                        @else
                            <td class="border">R$
                                {{ number_format($cycle->campaing->cold_chain_cost, 2, ',', ' ') }}
                            </td>
                        @endif

                    </tr>
                @endforeach

                @if ($total['before']['cold_chain'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal equipe da Rede de Frio </td>
                        <td class="border">R$ {{ number_format($total['before']['cold_chain'], 2, ',', ' ') }} </td>
                    </tr>
                @endif

                @foreach ($cycle->beforeDriverColdChains as $beforeDriverColdChain)
                    <tr>
                        <td class="border line-mat"> {{ $beforeDriverColdChain->registration }} </td>
                        <td class="border line-name"> {{ $beforeDriverColdChain->name }} </td>
                        <td class="border"> Motorista </td>
                        @if ($cycle->partial_value && $cycle->campaing->driver_cost > 0)
                            <td class="border">R$
                                {{ number_format(($cycle->campaing->driver_cost / 100) * $cycle->percentage_value, 2, ',', ' ') }}
                            </td>
                        @else
                            <td class="border">R$
                                {{ number_format($cycle->campaing->driver_cost, 2, ',', ' ') }}
                            </td>
                        @endif
                    </tr>
                @endforeach

                @if ($total['before']['driver_cold_chain'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal motorista </td>
                        <td class="border">R$ {{ number_format($total['before']['driver_cold_chain'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @endif

                @foreach ($cycle->beforeTransports as $beforeTransport)
                    <tr>
                        <td class="border line-mat"> {{ $beforeTransport->registration }} </td>
                        <td class="border line-name"> {{ $beforeTransport->name }} </td>
                        <td class="border"> Apoio da GETRANS </td>
                        @if ($cycle->partial_value && $cycle->campaing->transport_cost > 0)
                            <td class="border">R$
                                {{ number_format(($cycle->campaing->transport_cost / 100) * $cycle->percentage_value, 2, ',', ' ') }}
                            </td>
                        @else
                            <td class="border">R$
                                {{ number_format($cycle->campaing->transport_cost, 2, ',', ' ') }}
                            </td>
                        @endif

                    </tr>
                @endforeach

                @if ($total['before']['transport'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal apoio da GETRANS</td>
                        <td class="border">R$ {{ number_format($total['before']['transport'], 2, ',', ' ') }} </td>
                    </tr>
                @endif

                @foreach ($cycle->beforeZoonoses as $beforeZoonose)
                    <tr>
                        <td class="border line-mat"> {{ $beforeZoonose->registration }} </td>
                        <td class="border line-name"> {{ $beforeZoonose->name }} </td>
                        <td class="border"> Apoio da GEZOON </td>
                        @if ($cycle->partial_value && $cycle->campaing->zoonoses_cost > 0)
                            <td class="border">R$
                                {{ number_format(($cycle->campaing->zoonoses_cost / 100) * $cycle->percentage_value, 2, ',', ' ') }}
                            </td>
                        @else
                            <td class="border">R$
                                {{ number_format($cycle->campaing->zoonoses_cost, 2, ',', ' ') }}
                            </td>
                        @endif
                    </tr>
                @endforeach

                @if ($total['before']['zoonose'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal apoio da GEZOON</td>
                        <td class="border">R$ {{ number_format($total['before']['zoonose'], 2, ',', ' ') }} </td>
                    </tr>
                @endif

                @if ($total['before']['total'] > 0)
                    <tr>
                        <td class="border" colspan="3"> Total de {{ $before }}</td>
                        <td class="border">R$ {{ number_format($total['before']['total'], 2, ',', ' ') }} </td>
                    </tr>
                @endif



            </table>
        </div>
        <div class="footer">
            <hr />
            <div class="center">
                <address>
                    Rua Minas Gerais, Nº 909 – Bairro Matadouro. zona Norte. <br />
                    Teresina - PI, 64018-560
                </address>
            </div>
        </div>

    </div>
    <div style="page-break-after: always"></div>
    <div class="conteiner">
        <div class="date">
            <strong>{{ $currentDate }}</strong>
        </div>
        <div class="header">
            <div class="logo-header">
                <img src="img/logo_teresina.jpg" alt="logo">
            </div>
            <div class="logo-text">
                <strong>Prefeitura Municipal de Teresina</strong><br />
                <strong>Fundação Municipal de Saúde</strong><br />
                <strong>Gerência de Zoonoses GEZOON</strong><br />
                <strong>Núcleo de Controle da Raiva, Leishmaniose e Outras Zoonoses - NCRLOZ</strong><br />
            </div>
            <div class="center" style="text-align:center">
                <h3>Frequência</h3>
            </div>
        </div>

        <div class="content">

            <div>
                <strong> {{ $cycle->description }} </strong>
                <strong>- Folha de pagamento de {{ $start }} </strong>
            </div>
            <table class="table-vacination">
                <thead>
                    <th class="border">Mat.</th>
                    <th class="border">Nome</th>
                    <th class="border">Funçao</th>
                    <th class="border">Valor</th>
                </thead>
                @if ($cycle->coldChainCoordinator)
                    <tr>
                        <td class="border line-mat"> {{ $cycle->coldChainCoordinator->registration }} </td>
                        <td class="border line-name"> {{ $cycle->coldChainCoordinator->name }} </td>
                        <td class="border"> Coordenador da Rede de Frio</td>
                        <td class="border">
                            R$ {{ number_format($cycle->campaing->cold_chain_coordinator_cost, 2, ',', ' ') }}
                        </td>
                    </tr>
                @endif
                @if ($cycle->coldChainNurse)
                    <tr>
                        <td class="border line-mat"> {{ $cycle->coldChainNurse->registration }} </td>
                        <td class="border line-name"> {{ $cycle->coldChainNurse->name }} </td>
                        <td class="border"> Enfermeira da Rede de Frio</td>
                        <td class="border">
                            R$ {{ number_format($cycle->campaing->cold_chain_nurse_cost, 2, ',', ' ') }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td class="border" colspan="4"></td>
                </tr>
                @foreach ($cycle->startColdChains as $startColdChain)
                    <tr>
                        <td class="border line-mat"> {{ $startColdChain->registration }} </td>
                        <td class="border line-name"> {{ $startColdChain->name }} </td>
                        <td class="border"> Equipe da Rede de Frio </td>
                        <td class="border">R$ {{ number_format($cycle->campaing->cold_chain_cost, 2, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach

                @if ($total['start']['cold_chain'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal equipe da Rede de Frio </td>
                        <td class="border">R$ {{ number_format($total['start']['cold_chain'], 2, ',', ' ') }} </td>
                    </tr>
                @endif

                @foreach ($cycle->startDriverColdChains as $startDriverColdChain)
                    <tr>
                        <td class="border line-mat"> {{ $startDriverColdChain->registration }} </td>
                        <td class="border line-name"> {{ $startDriverColdChain->name }} </td>
                        <td class="border"> Motorista da Rede de Frio </td>
                        <td class="border">R$ {{ number_format($cycle->campaing->driver_cost, 2, ',', ' ') }} </td>
                    </tr>
                @endforeach

                @if ($total['start']['driver_cold_chain'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal motorista </td>
                        <td class="border">R$ {{ number_format($total['start']['driver_cold_chain'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @endif

                @foreach ($cycle->startTransports as $startTransport)
                    <tr>
                        <td class="border line-mat"> {{ $startTransport->registration }} </td>
                        <td class="border line-name"> {{ $startTransport->name }} </td>
                        <td class="border"> Apoio da GETRANS </td>
                        <td class="border">R$ {{ number_format($cycle->campaing->transport_cost, 2, ',', ' ') }} </td>
                    </tr>
                @endforeach

                @if ($total['start']['transport'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal apoio da GETRANS</td>
                        <td class="border">R$ {{ number_format($total['start']['transport'], 2, ',', ' ') }} </td>
                    </tr>
                @endif

                @foreach ($cycle->startZoonoses as $startZoonose)
                    <tr>
                        <td class="border line-mat"> {{ $startZoonose->registration }} </td>
                        <td class="border line-name"> {{ $startZoonose->name }} </td>
                        <td class="border"> Apoio da GEZOON </td>
                        <td class="border">R$ {{ number_format($cycle->campaing->zoonoses_cost, 2, ',', ' ') }} </td>
                    </tr>
                @endforeach

                @if ($total['start']['zoonose'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal apoio da GEZOON</td>
                        <td class="border">R$ {{ number_format($total['start']['zoonose'], 2, ',', ' ') }} </td>
                    </tr>
                @endif

                @if (!$cycle->supports[0]->is_rural)
                    @foreach ($cycle->supports as $support)
                        @if ($support->coordinator)
                            <tr>
                                <td class="border line-mat"> {{ $support->coordinator->registration }} </td>
                                <td class="border line-name"> {{ $support->coordinator->name }} </td>
                                <td class="border"> Coordenador</td>
                                <td class="border">R$
                                    {{ number_format($cycle->campaing->coordinator_cost, 2, ',', ' ') }}
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    @if ($total['start']['coordinator'] > 0)
                        <tr>
                            <td class="border" colspan="3"> SubTotal Coordenador</td>
                            <td class="border">R$ {{ number_format($total['start']['coordinator'], 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endif

                @endif
                @foreach ($cycle->supports as $support)
                    @if ($support->is_rural)
                        @foreach ($support->ruralSupervisors as $ruralSupervisor)
                            <tr>
                                <td class="border line-mat"> {{ $ruralSupervisor->registration }} </td>
                                <td class="border line-name"> {{ $ruralSupervisor->name }} </td>
                                <td class="border"> Supervisor Rural</td>
                                <td class="border">R$
                                    {{ number_format($cycle->campaing->rural_supervisor_cost, 2, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($support->supervisors as $supervisor)
                            <tr>
                                <td class="border line-mat"> {{ $supervisor->registration }} </td>
                                <td class="border line-name"> {{ $supervisor->name }} </td>
                                <td class="border"> Supervisor</td>
                                <td class="border">R$
                                    {{ number_format($cycle->campaing->supervisor_cost, 2, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                @if ($cycle->supports[0]->is_rural)
                    @if ($total['start']['rural_supervisor'] > 0)
                        <tr>
                            <td class="border" colspan="3"> SubTotal Supervisor Rural</td>
                            <td class="border">R$
                                {{ number_format($total['start']['rural_supervisor'], 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endif
                @else
                    @if ($total['start']['supervisor'] > 0)
                        <tr>
                            <td class="border" colspan="3"> SubTotal Supervisor</td>
                            <td class="border">R$ {{ number_format($total['start']['supervisor'], 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endif

                @endif


                @foreach ($cycle->supports as $support)
                    @if ($support->is_rural)
                        @foreach ($support->ruralAssistants as $ruralAssistant)
                            <tr>
                                <td class="border line-mat"> {{ $ruralAssistant->registration }} </td>
                                <td class="border line-name"> {{ $ruralAssistant->name }} </td>
                                <td class="border"> Auxiliar Rural </td>
                                <td class="border">R$
                                    {{ number_format($cycle->campaing->rural_assistant_cost, 2, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($support->assistants as $assistant)
                            <tr>
                                <td class="border line-mat"> {{ $assistant->registration }} </td>
                                <td class="border line-name"> {{ $assistant->name }} </td>
                                <td class="border"> Auxiliar </td>
                                <td class="border">R$
                                    {{ number_format($cycle->campaing->assistant_cost, 2, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                @if ($cycle->supports[0]->is_rural)
                    @if ($total['start']['rural_assistant'] > 0)
                        <tr>
                            <td class="border" colspan="3"> SubTotal Auxiliar Rural</td>
                            <td class="border">R$ {{ number_format($total['start']['rural_assistant'], 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endif
                @else
                    @if ($total['start']['assistant'] > 0)
                        <tr>
                            <td class="border" colspan="3"> SubTotal Auxiliar</td>
                            <td class="border">R$ {{ number_format($total['start']['assistant'], 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endif

                @endif

                @foreach ($cycle->supports as $support)
                    @foreach ($support->drivers as $driver)
                        <tr>
                            <td class="border line-mat"> {{ $driver->registration }} </td>
                            <td class="border line-name"> {{ $driver->name }} </td>
                            <td class="border"> Motorista do Ponto de Apoio</td>
                            <td class="border">R$
                                {{ number_format($cycle->campaing->driver_cost, 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                @if ($total['start']['driver'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal Motorista </td>
                        <td class="border">R$ {{ number_format($total['start']['driver'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @endif

                @foreach ($cycle->supports as $support)
                    @foreach ($support->vaccinators as $vaccinator)
                        <tr>
                            <td class="border line-mat"> {{ $vaccinator->registration }} </td>
                            <td class="border line-name"> {{ $vaccinator->name }} </td>
                            <td class="border"> Vacinador</td>
                            <td class="border">R$
                                {{ number_format($cycle->campaing->vaccinator_cost, 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endforeach
                    @foreach ($support->points as $point)
                        @foreach ($point->vaccinators as $vaccinator)
                            <tr>
                                <td class="border line-mat"> {{ $vaccinator->registration }} </td>
                                <td class="border line-name"> {{ $vaccinator->name }} </td>
                                <td class="border"> Vacinador</td>
                                <td class="border">R$
                                    {{ number_format($cycle->campaing->vaccinator_cost, 2, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach

                @if ($total['start']['vaccinator'] > 0)
                    <tr>
                        <td class="border" colspan="3"> SubTotal Vacinadores</td>
                        <td class="border">R$ {{ number_format($total['start']['vaccinator'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @endif

                @if (!$cycle->supports[0]->is_rural)
                    @foreach ($cycle->supports as $support)
                        @foreach ($support->points as $point)
                            @foreach ($point->annotators as $annotator)
                                <tr>
                                    <td class="border line-mat"> {{ $annotator->registration }} </td>
                                    <td class="border line-name"> {{ $annotator->name }} </td>
                                    <td class="border"> Anotador</td>
                                    <td class="border">R$
                                        {{ number_format($cycle->campaing->annotators_cost, 2, ',', ' ') }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach

                    @if ($total['start']['annotator'] > 0)
                        <tr>
                            <td class="border" colspan="3"> SubTotal Anotador</td>
                            <td class="border">R$ {{ number_format($total['start']['annotator'], 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endif

                @endif

                @if ($total['start']['total'] > 0)
                    <tr>
                        <td class="border" colspan="3"> Total de {{ $before }}</td>
                        <td class="border">R$ {{ number_format($total['start']['total'], 2, ',', ' ') }} </td>
                    </tr>
                @endif

            </table>


        </div>
        <div class="footer">
            <hr />
            <div class="center">
                <address>
                    Rua Minas Gerais, Nº 909 – Bairro Matadouro. zona Norte. <br />
                    Teresina - PI, 64018-560
                </address>
            </div>
        </div>

    </div>
    <div style="page-break-after: always"></div>
    <div class="conteiner">
        <div class="date">
        </div>
        <div class="header">
            <div class="logo-header">
                <img src="img/logo_teresina.jpg" alt="logo">
            </div>
            <div class="logo-text">
                <strong>Prefeitura Municipal de Teresina</strong><br />
                <strong>Fundação Municipal de Saúde</strong><br />
                <strong>Gerência de Zoonoses GEZOON</strong><br />
                <strong>Núcleo de Controle da Raiva, Leishmaniose e Outras Zoonoses - NCRLOZ</strong><br />
            </div>
            <div class="center" style="text-align:center">
                <div>
                    <strong> Consolidado custos dá {{ $cycle->description }} </strong>
                    <strong>- Folha de pagamento de {{ $start }} </strong>
                </div>
            </div>
        </div>

        <div class="content">
            <table class="table-vacination">
                <thead>
                    <th class="border">Descrição</th>
                    <th class="border">Qt.</th>
                    <th class="border">Valor</th>
                    <th class="border">Subtotal</th>
                </thead>
                @if ($total['cycle']['cold_chain_coordinator'] > 0)
                    <tr>
                        <td class="border"> Coordenador da Rede de Frio </td>
                        <td class="border"> {{ $count['cold_chain_coordinator'] }}</td>
                        <td class="border"> {{ $cycle->campaing->cold_chain_coordinator_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['cold_chain_coordinator'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['cold_chain_nurse'] > 0)
                    <tr>
                        <td class="border"> Emfermeira da Rede de Frio</td>
                        <td class="border"> {{ $count['cold_chain_nurse'] }}</td>
                        <td class="border"> {{ $cycle->campaing->cold_chain_nurse_cost }}</td>
                        <td class="border">R$ {{ number_format($total['cycle']['cold_chain_nurse'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @endif
                @if ($total['cycle']['cold_chain'] > 0)
                    <tr>
                        <td class="border"> Equipe da Rede de Frio </td>
                        <td class="border"> {{ $count['cold_chain'] }}</td>
                        <td class="border"> {{ $cycle->campaing->cold_chain_cost }}</td>
                        <td class="border">R$ {{ number_format($total['cycle']['cold_chain'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['driver_cold_chain'] > 0)
                    <tr>
                        <td class="border"> Motorista </td>
                        <td class="border"> {{ $count['driver_cold_chain'] }}</td>
                        <td class="border"> {{ $cycle->campaing->driver_cost }}</td>
                        <td class="border">R$ {{ number_format($total['cycle']['driver_cold_chain'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @endif
                @if ($total['cycle']['transport'] > 0)
                    <tr>
                        <td class="border"> Equipe da GETRANS </td>
                        <td class="border"> {{ $count['transport'] }}</td>
                        <td class="border"> {{ $cycle->campaing->transport_cost }}</td>
                        <td class="border">R$ {{ number_format($total['cycle']['transport'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['transport'] > 0)
                    <tr>
                        <td class="border"> Equipe de apoio GEZOON </td>
                        <td class="border"> {{ $count['zoonose'] }}</td>
                        <td class="border"> {{ $cycle->campaing->zoonoses_cost }}</td>
                        <td class="border">R$ {{ number_format($total['cycle']['zoonose'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['statistic_coordinator'] > 0)
                    <tr>
                        <td class="border"> Coordenador da Estatística</td>
                        <td class="border"> {{ $count['statistic_coordinator'] }}</td>
                        <td class="border"> {{ $cycle->campaing->statistic_coordinator_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['statistic_coordinator'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['statistic'] > 0)
                    <tr>
                        <td class="border"> Equipe da Estatística</td>
                        <td class="border"> {{ $count['statistic'] }}</td>
                        <td class="border"> {{ $cycle->campaing->statistic_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['statistic'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['coordinator'] > 0)
                    <tr>
                        <td class="border"> Coordenadores de Ponto de Apoio</td>
                        <td class="border"> {{ $count['coordinator'] }}</td>
                        <td class="border"> {{ $cycle->campaing->coordinator_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['coordinator'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['rural_supervisor'] > 0)
                    <tr>
                        <td class="border"> Supervisores Rurais</td>
                        <td class="border"> {{ $count['rural_supervisor'] }}</td>
                        <td class="border"> {{ $cycle->campaing->rural_supervisor_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['rural_supervisor'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['rural_assistant'] > 0)
                    <tr>
                        <td class="border"> Auxiliares Rurais</td>
                        <td class="border"> {{ $count['rural_assistant'] }}</td>
                        <td class="border"> {{ $cycle->campaing->rural_assistant_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['rural_assistant'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['supervisor'] > 0)
                    <tr>
                        <td class="border"> Supervisores </td>
                        <td class="border"> {{ $count['supervisor'] }}</td>
                        <td class="border"> {{ $cycle->campaing->supervisor_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['supervisor'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['assistant'] > 0)
                    <tr>
                        <td class="border"> Auxiliares </td>
                        <td class="border"> {{ $count['assistant'] }}</td>
                        <td class="border"> {{ $cycle->campaing->assistant_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['assistant'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['vaccinator'] > 0)
                    <tr>
                        <td class="border"> Vacinadores </td>
                        <td class="border"> {{ $count['vaccinator'] }}</td>
                        <td class="border"> {{ $cycle->campaing->vaccinator_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['vaccinator'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['annotator'] > 0)
                    <tr>
                        <td class="border"> Anotadores </td>
                        <td class="border"> {{ $count['annotator'] }}</td>
                        <td class="border"> {{ $cycle->campaing->annotators_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['annotator'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['driver'] > 0)
                    <tr>
                        <td class="border"> Motoristass da campanha </td>
                        <td class="border"> {{ $count['driver'] }}</td>
                        <td class="border"> {{ $cycle->campaing->driver_cost }}</td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['driver'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
                @if ($total['cycle']['total'] > 0)
                    <tr>
                        <td class="border" colspan="3"> Total da Etapa </td>
                        <td class="border">R$
                            {{ number_format($total['cycle']['total'], 2, ',', ' ') }} </td>
                    </tr>
                @endif
            </table>





        </div>
        <div class="footer">
            <hr />
            <div class="center">
                <address>
                    Rua Minas Gerais, Nº 909 – Bairro Matadouro. zona Norte. <br />
                    Teresina - PI, 64018-560
                </address>
            </div>
        </div>
    </div>
</body>

</html>
