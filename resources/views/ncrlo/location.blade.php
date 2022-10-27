<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Relatório de Locação de Pessoal</title>
    <style>
        .conteiner {
            margin: 5% 3%;
        }

        .date {
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
    </style>
</head>

<body>
    <div class="conteiner">
        <div class="date">
            <strong>{{ $today }}</strong>
        </div>
        <div class="header">
            <div class="logo-header">
                <img src="img/logo_teresina.jpg" alt="logo">
            </div>
            <div class="logo-text">
                <strong>Prefeitura Municipal de Teresina</strong><br />
                <strong>Fundação Municipal de Saúde</strong><br />
                <strong>Gerência de Zoonoses GEZOON</strong><br />
                <strong>Núcleo de Controle da Raiva, Leishmaniose e Outras Zoonoses - NCRLO</strong><br />
            </div>
        </div>
        <br />
        <br />
        <div class="content">
            <h3>{{ $cycle->description }} Início: {{ $cycle->start }} - Fim: {{ $cycle->end }}</h3>
            <ol>
                @foreach ($cycle->supports as $support)
                    <li>
                        @if ($support->is_rural)
                            <strong> Área: {{ $support->support->name }} </strong>
                        @else
                            <strong> Ponto de Apoio: {{ $support->support->name }} </strong>
                        @endif

                        <br />
                        @isset($support->coordinator->name)
                            <strong> Coordenador: {{ $support->coordinator->name }} </strong>
                        @endisset

                        @if (count($support->supervisors) > 0)
                            <br />
                            <strong>Supervisores: </strong>
                            <ul>
                                @foreach ($support->supervisors as $supervisor)
                                    <li>{{ $supervisor->name }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (count($support->ruralSupervisors) > 0)
                            <br />
                            <strong>Supervisores: </strong>
                            <ul>
                                @foreach ($support->ruralSupervisors as $ruralSupervisor)
                                    <li>{{ $ruralSupervisor->name }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (count($support->ruralAssistants) > 0)
                            <br />
                            <strong>Auxiliares: </strong>
                            <ul>
                                @foreach ($support->ruralAssistants as $ruralAssistant)
                                    <li>{{ $ruralAssistant->name }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (count($support->assistants) > 0)
                            <br />
                            <strong>Apoiadores: </strong>
                            <ul>
                                @foreach ($support->assistants as $assistant)
                                    <li>{{ $assistant->name }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (count($support->drivers) > 0)
                            <br />
                            <strong>Motoristas: </strong>
                            <ul>
                                @foreach ($support->drivers as $driver)
                                    <li>{{ $driver->name }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (count($support->vaccinators) > 0)
                            <br />
                            @if ($support->is_rural)
                                <strong>Vacinadores: </strong>
                            @else
                                <strong>Vacinadores reserva: </strong>
                            @endif

                            <ul>
                                @foreach ($support->vaccinators as $vaccinator)
                                    <li>{{ $vaccinator->name }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <br />
                        @if ($support->is_rural)
                            <strong> Localidade: </strong>
                        @else
                            <strong>Postos de Vacina:</strong>
                        @endif

                        <br />
                        <ul>
                            @foreach ($support->points as $point)
                                <li>
                                    @if ($support->is_rural)
                                        <strong> {{ $point->point->name }} </strong>
                                    @else
                                        <strong> Posto: {{ $point->point->name }} </strong>
                                    @endif

                                    <br />
                                    @isset($point->supervisor->name)
                                        <strong> Supervisor: {{ $point->supervisor->name }} </strong>
                                    @endisset

                                    @if (count($point->vaccinators) > 0)
                                        <br />
                                        <strong>Vacidadores: </strong>
                                        <ul>
                                            @foreach ($point->vaccinators as $vaccinator)
                                                <li>{{ $vaccinator->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if (count($point->vaccinators) > 0)
                                        <br />
                                        <strong>Anotadores: </strong>
                                        <ul>
                                            @foreach ($point->annotators as $annotator)
                                                <li>{{ $annotator->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <br />
                                </li>
                            @endforeach
                        </ul>
                        <br />
                    </li>
                @endforeach
            </ol>

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
