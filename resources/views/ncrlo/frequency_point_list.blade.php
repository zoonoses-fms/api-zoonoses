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
                <h2>Frequência</h2>
            </div>
        </div>

        <div class="content">
            <div>
                <strong> Posto: {{ $point->point->name }}  - Área: {{ $point->area }} - Ordem: {{ $point->order }}</strong>
            </div>

            @isset($point->supervisor->name)
                <div>
                    <strong>Supervisor: </strong>
                </div>
                <table>
                    <tr>
                        <td class="name">
                            {{ $point->supervisor->registration }} -
                            {{ $point->supervisor->name }}
                            -
                            {{ $point->supervisor->phone }}
                        </td>
                        <td class="line">
                        </td>
                    </tr>
                </table>
            @endisset

            @if (count($point->vaccinators) > 0)
                <div>
                    <strong>Vacinadores: </strong>
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
                    @foreach ($point->vaccinators as $vaccinator)
                        <tr>
                            <td class="border line-mat"> {{ $vaccinator->registration }} </td>
                            <td class="border line-name"> {{ $vaccinator->name }} </td>
                            <td class="border"> {{ $vaccinator->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if (count($point->annotators) > 0)
                <div>
                    <strong>Anotadores: </strong>
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
                    @foreach ($point->annotators as $annotator)
                        <tr>
                            <td class="border line-mat"> {{ $annotator->registration }} </td>
                            <td class="border line-name"> {{ $annotator->name }} </td>
                            <td class="border"> {{ $annotator->phone }} </td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-origin"></td>
                            <td class="border line-vaccinator"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

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
