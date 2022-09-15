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
                <p>Gerência de Zoonoses GEZOON</p>
                <p>Fundação Municipal de Saúde</p>
                <p>Núcleo de Controle da Raiva, Leishmaniose e Outras Zoonoses - NCRLO</p>
            </div>
        </div>
        <br />
        <br />
        <div class="content">
            <h5>{{ $cycle->description }} Início: {{ $cycle->start }} - Fim: {{ $cycle->end }}</h5>
            <ol>
                @foreach ($cycle->supports as $support)
                    <li>
                        <strong> Ponto de Apoio: {{ $support->support->name }} </strong>
                        <br />
                        @isset($support->coordinator->name)
                            <strong> Coordenador: {{ $support->coordinator->name }} </strong>
                        @endisset
                        <br />
                        <strong>Supervisores: </strong>
                        <ul>
                            @foreach ($support->supervisors as $supervisor)
                                <li>{{ $supervisor->name }}</li>
                            @endforeach
                        </ul>
                        <strong>Auxiliares: </strong>
                        <ul>
                            @foreach ($support->assistants as $assistant)
                                <li>{{ $assistant->name }}</li>
                            @endforeach
                        </ul>
                        <strong>Motoristas: </strong>
                        <ul>
                            @foreach ($support->drivers as $driver)
                                <li>{{ $driver->name }}</li>
                            @endforeach
                        </ul>
                        <strong>Vacinadores reserva: </strong>
                        <ul>
                            @foreach ($support->vaccinators as $vaccinator)
                                <li>{{ $vaccinator->name }}</li>
                            @endforeach
                        </ul>
                        <strong>Postos de Vacina</strong>
                        <ul>
                            @foreach ($support->points as $point)
                                <li>
                                    <strong> Ponto de Apoio: {{ $point->point->name }} </strong>
                                    <br />
                                    @isset($point->supervisor->name)
                                        <strong> Supervisor: {{ $point->supervisor->name }} </strong>
                                    @endisset
                                    <br />
                                    <strong>Vacidadores: </strong>
                                    <ul>
                                        @foreach ($point->vaccinators as $vaccinator)
                                            <li>{{ $vaccinator->name }}</li>
                                        @endforeach
                                    </ul>
                                    <strong>Anotadores: </strong>
                                    <ul>
                                        @foreach ($point->annotators as $annotator)
                                            <li>{{ $annotator->name }}</li>
                                        @endforeach
                                    </ul>
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
