<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Relatório de Locação de Pessoal</title>
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
            width: 25%;
        }
    </style>
</head>

<body>
    @foreach ($managements as $management)
        @for ($i = 0; $i < count($dates); $i++)
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
                        <strong>Núcleo de Controle da Raiva, Leishmaniose e Outras Zoonoses - NCRLOZ</strong><br />
                    </div>
                    <div class="center" style="text-align:center">
                        <span>Frequência - {{ $management }} - {{ $dates[$i] }}</span>
                    </div>
                    <div>
                        <strong> {{ $cycle->description }} </strong>
                    </div>

                </div>

                <div class="content">


                    @foreach ($cycle->profiles as $profile)
                        @if ($profile->management == $management)
                            @if (isset($profile->workers[$i]) && count($profile->workers[$i]) > 0)
                                <div>
                                    <strong>{{ $profile->name }} - {{ $dates[$i] }}</strong>
                                    <table class="table-vacination">
                                        <thead>
                                            <th class="border">Mat.</th>
                                            <th class="border">Nome</th>
                                            <th class="border">Fone</th>
                                            <th class="border">FMS</th>
                                            <th class="border">ACE</th>
                                            <th class="border">ACS</th>
                                            <th class="border">Assinatura</th>
                                        </thead>
                                        @foreach ($profile->workers[$i] as $worker)
                                            <tr>
                                                <td class="border line-mat"> {{ $worker->registration }} </td>
                                                <td class="border line-name"> {{ $worker->name }} </td>
                                                <td class="border"> {{ $worker->phone }} </td>
                                                <td class="border line-origin"></td>
                                                <td class="border line-origin"></td>
                                                <td class="border line-origin"></td>
                                                <td class="border line-vaccinator"></td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                        @endif
                    @endforeach


                    <div>
                        <strong>Colaboradores: </strong>
                    </div>
                    <table class="table-vacination">
                        <thead>
                            <th class="border">Mat.</th>
                            <th class="border">Nome</th>
                            <th class="border">Fone</th>
                            <th class="border">FMS</th>
                            <th class="border">ACE</th>
                            <th class="border">ACS</th>
                            <th class="border">Assinatura</th>
                        </thead>
                        <tr>
                            <td class="border line-mat"></td>
                            <td class="border line-name"></td>
                            <td class="border"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                        <tr>
                            <td class="border line-mat"></td>
                            <td class="border line-name"></td>
                            <td class="border"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                        <tr>
                            <td class="border line-mat"></td>
                            <td class="border line-name"></td>
                            <td class="border"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                        <tr>
                            <td class="border line-mat"></td>
                            <td class="border line-name"></td>
                            <td class="border"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                        <tr>
                            <td class="border line-mat"></td>
                            <td class="border line-name"></td>
                            <td class="border"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>

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
            @if (!$i == count($dates) - 1)
                <div style="page-break-after: always"></div>
            @endif
        @endfor
        @if (!$loop->last)
            <div style="page-break-after: always"></div>
        @endif

    @endforeach

</body>

</html>
