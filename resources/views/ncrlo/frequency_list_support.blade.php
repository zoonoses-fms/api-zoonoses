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
                <span>Frequência</span>
            </div>
        </div>

        <div class="content">

            @if (count($cycle->beforeColdChains) > 0)
                <div>
                    <strong> {{ $cycle->description }} </strong>
                    <strong>- Rede de Frio {{ $before }} </strong>
                </div>
                <table>
                    <tr>
                        <td class="name">
                            Coordenador: {{ $cycle->coldChainCoordinator->registration }} -
                            {{ $cycle->coldChainCoordinator->name }} -
                            {{ $cycle->coldChainCoordinator->phone }}
                        </td>
                        <td class="line">
                        </td>
                    </tr>
                </table>
                <table style="margin-bottom: 10px">
                    <tr>
                        <td class="name">
                            Enfermeira: {{ $cycle->coldChainNurse->registration }} - {{ $cycle->coldChainNurse->name }}
                            -
                            {{ $cycle->coldChainNurse->phone }}
                        </td>
                        <td class="line">
                        </td>
                    </tr>
                </table>
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
                    @foreach ($cycle->beforeColdChains as $beforeColdChain)
                        <tr>
                            <td class="border line-mat"> {{ $beforeColdChain->registration }} </td>
                            <td class="border line-name"> {{ $beforeColdChain->name }} </td>
                            <td class="border"> {{ $beforeColdChain->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <div>
                <strong>Motoristas: </strong>
            </div>
            @if (count($cycle->beforeDriverColdChains) > 0)

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
                    @foreach ($cycle->beforeDriverColdChains as $beforeDriverColdChain)
                        <tr>
                            <td class="border line-mat"> {{ $beforeDriverColdChain->registration }} </td>
                            <td class="border line-name"> {{ $beforeDriverColdChain->name }} </td>
                            <td class="border"> {{ $beforeDriverColdChain->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

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
                <span>Frequência</span>
            </div>
        </div>

        <div class="content">

            @if (count($cycle->startColdChains) > 0)
                <div>
                    <strong> {{ $cycle->description }} </strong>
                    <strong>- Rede de Frio {{ $start }} </strong>
                </div>
                <table>
                    <tr>
                        <td class="name">
                            Coordenador: {{ $cycle->coldChainCoordinator->registration }} -
                            {{ $cycle->coldChainCoordinator->name }} -
                            {{ $cycle->coldChainCoordinator->phone }}
                        </td>
                        <td class="line">
                        </td>
                    </tr>
                </table>
                <table style="margin-bottom: 10px">
                    <tr>
                        <td class="name">
                            Enfermeira: {{ $cycle->coldChainNurse->registration }} -
                            {{ $cycle->coldChainNurse->name }} -
                            {{ $cycle->coldChainNurse->phone }}
                        </td>
                        <td class="line">
                        </td>
                    </tr>
                </table>
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
                    @foreach ($cycle->startColdChains as $startColdChain)
                        <tr>
                            <td class="border line-mat"> {{ $startColdChain->registration }} </td>
                            <td class="border line-name"> {{ $startColdChain->name }} </td>
                            <td class="border"> {{ $startColdChain->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <div>
                <strong>Motoristas: </strong>
            </div>
            @if (count($cycle->startDriverColdChains) > 0)

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
                    @foreach ($cycle->startDriverColdChains as $startDriverColdChain)
                        <tr>
                            <td class="border line-mat"> {{ $startDriverColdChain->registration }} </td>
                            <td class="border line-name"> {{ $startDriverColdChain->name }} </td>
                            <td class="border"> {{ $startDriverColdChain->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

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
                <span>Frequência</span>
            </div>
        </div>

        <div class="content">

            @if (count($cycle->statistics) > 0)
                <div>
                    <strong> {{ $cycle->description }} </strong>
                    <strong>- Estatística {{ $start }} </strong>
                </div>
                <table style="margin-bottom: 10px">
                    <tr>
                        <td class="name">
                            Coordenador: {{ $cycle->statisticCoordinator->registration }} -
                            {{ $cycle->statisticCoordinator->name }} -
                            {{ $cycle->statisticCoordinator->phone }}
                        </td>
                        <td class="line">
                        </td>
                    </tr>
                </table>
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
                    @foreach ($cycle->statistics as $statistic)
                        <tr>
                            <td class="border line-mat"> {{ $statistic->registration }} </td>
                            <td class="border line-name"> {{ $statistic->name }} </td>
                            <td class="border"> {{ $statistic->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

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
                <span>Frequência</span>
            </div>
        </div>

        <div class="content">

            @if (count($cycle->beforeTransports) > 0)
                <div>
                    <strong> {{ $cycle->description }} </strong>
                    <strong>- Apoio GETRANS {{ $before }} </strong>
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
                    @foreach ($cycle->beforeTransports as $transport)
                        <tr>
                            <td class="border line-mat"> {{ $transport->registration }} </td>
                            <td class="border line-name"> {{ $transport->name }} </td>
                            <td class="border"> {{ $transport->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

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
                <span>Frequência</span>
            </div>
        </div>

        <div class="content">

            @if (count($cycle->startTransports) > 0)
                <div>
                    <strong> {{ $cycle->description }} </strong>
                    <strong>- Apoio GETRANS {{ $start }} </strong>
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
                    @foreach ($cycle->startTransports as $transport)
                        <tr>
                            <td class="border line-mat"> {{ $transport->registration }} </td>
                            <td class="border line-name"> {{ $transport->name }} </td>
                            <td class="border"> {{ $transport->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

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
                <span>Frequência</span>
            </div>
        </div>

        <div class="content">

            @if (count($cycle->beforeZoonoses) > 0)
                <div>
                    <strong> {{ $cycle->description }} </strong>
                    <strong>- Apoio GEZOON {{ $before }} </strong>
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
                    @foreach ($cycle->beforeZoonoses as $zoonose)
                        <tr>
                            <td class="border line-mat"> {{ $zoonose->registration }} </td>
                            <td class="border line-name"> {{ $zoonose->name }} </td>
                            <td class="border"> {{ $zoonose->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

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
                <span>Frequência</span>
            </div>
        </div>

        <div class="content">

            @if (count($cycle->startZoonoses) > 0)
                <div>
                    <strong> {{ $cycle->description }} </strong>
                    <strong>- Apoio GEZOON {{ $start }} </strong>
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
                    @foreach ($cycle->startZoonoses as $zoonose)
                        <tr>
                            <td class="border line-mat"> {{ $zoonose->registration }} </td>
                            <td class="border line-name"> {{ $zoonose->name }} </td>
                            <td class="border"> {{ $zoonose->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

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
</body>

</html>
