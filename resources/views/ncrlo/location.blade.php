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
            @foreach ($cycle->profiles as $profile)
                <h4>{{ $profile->name }}</h4>

                @for ($i = 0; $i < count($profile->workers); $i++)
                    <strong>{{ $dates[$i] }}</strong>
                    <ul>
                        @foreach ($profile->workers[$i] as $worker)
                            <li> {{ $worker->name }}</li>
                        @endforeach
                    </ul>
                @endfor
            @endforeach

            @foreach ($cycle->supports as $support)
                <strong><u>Ponto de apoio: {{ $support->support->name }}</u></strong>
                @foreach ($support->profiles as $profile)
                    <h4>{{ $profile->name }}</h4>

                    @for ($i = 0; $i < count($profile->workers); $i++)
                        @if ($profile->is_pre_campaign > 0)
                            <strong>{{ $dates[$i] }}</strong>
                        @endif

                        <ul>
                            @foreach ($profile->workers[$i] as $worker)
                                <li> {{ $worker->name }}</li>
                            @endforeach
                        </ul>
                    @endfor
                @endforeach
                @foreach ($support->points as $point)
                    <strong><u>Posto de vacina: {{ $point->point->name }}</u></strong>
                    @foreach ($point->profiles as $profile)
                        <h4>{{ $profile->name }}</h4>

                        @for ($i = 0; $i < count($profile->workers); $i++)
                            @if ($profile->is_pre_campaign > 0)
                                <strong>{{ $dates[$i] }}</strong>
                            @endif
                            <ul>
                                @foreach ($profile->workers[$i] as $worker)
                                    <li> {{ $worker->name }}</li>
                                @endforeach
                            </ul>
                        @endfor
                    @endforeach
                @endforeach
            @endforeach


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
