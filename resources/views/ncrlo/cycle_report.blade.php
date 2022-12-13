<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Relatório de Vicinação</title>
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
            margin: 5px auto;
            font-size: 12px;
        }

        .border {
            border: 1px solid black;
            border-collapse: collapse;
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
                <h2>Relatório de Vacinação</h2>
            </div>
        </div>

        <div class="content">
            <div>
                <h3>{{ $cycle->description }} de {{ date('d-m-Y', strtotime($cycle->start)) }} até
                    {{ date('d-m-Y', strtotime($cycle->end)) }} </h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="border">Cães</th>
                        <th class="border">Cadelas</th>
                        <th class="border">Total Cães</th>
                        <th class="border">Gatos</th>
                        <th class="border">Gatas</th>
                        <th class="border">Total Gatos</th>
                        <th class="border">Total</th>
                        <th class="border">Meta</th>
                        <th class="border">Cobertura</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border"> {{ number_format($cycle->male_dogs, 0, ',', '.') }} </td>

                        <td class="border"> {{ number_format($cycle->female_dogs, 0, ',', '.') }} </td>

                        <td class="border"> {{ number_format($cycle->total_of_dogs, 0, ',', '.') }} </td>

                        <td class="border"> {{ number_format($cycle->male_cat, 0, ',', '.') }} </td>

                        <td class="border"> {{ number_format($cycle->female_cat, 0, ',', '.') }} </td>

                        <td class="border"> {{ number_format($cycle->total_of_cats, 0, ',', '.') }} </td>

                        <td class="border"> {{ number_format($cycle->total, 0, ',', '.') }} </td>

                        <td class="border"> {{ number_format($cycle->goal, 0, ',', '.') }} </td>
                        @if ($cycle->total > 0 && $cycle->goal > 0)
                            <td class="border">
                                {{ number_format($cycle->total / ($cycle->goal / 100), 2, ',', ' ') }}%
                            </td>
                        @else
                            <td class="border"> 0% </td>
                        @endif
                        < </tr>
                </tbody>
            </table>

            @if (count($cycle->saads) > 0)
                <table>
                    <thead>
                        <tr>
                            <th class="border">Nome</th>
                            <th class="border">Cães</th>
                            <th class="border">Cadelas</th>
                            <th class="border">Total Cães</th>
                            <th class="border">Gatos</th>
                            <th class="border">Gatas</th>
                            <th class="border">Total Gatos</th>
                            <th class="border">Total</th>
                            <th class="border">Meta</th>
                            <th class="border">Cobertura</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cycle->saads as $saad)
                            <tr>
                                <td class="border"> {{ $saad->name }} </td>

                                <td class="border"> {{ number_format($saad->male_dogs, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($saad->female_dogs, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($saad->total_of_dogs, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($saad->male_cat, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($saad->female_cat, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($saad->total_of_cats, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($saad->total, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($saad->goal, 0, ',', '.') }} </td>
                                <td class="border">
                                    {{ number_format($saad->total / ($saad->goal / 100), 2, ',', ' ') }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if (count($cycle->supports) > 0)
                @foreach ($cycle->supports as $support)
                    <div>
                        <h4> {{ $support->support->name }} - {{ $support->saads[0]->name }} </h4>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th class="border">Cães</th>
                                <th class="border">Cadelas</th>
                                <th class="border">Total Cães</th>
                                <th class="border">Gatos</th>
                                <th class="border">Gatas</th>
                                <th class="border">Total Gatos</th>
                                <th class="border">Total</th>
                                <th class="border">Meta</th>
                                <th class="border">Cobertura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border"> {{ number_format($support->male_dogs, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($support->female_dogs, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($support->total_of_dogs, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($support->male_cat, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($support->female_cat, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($support->total_of_cats, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($support->total, 0, ',', '.') }} </td>

                                <td class="border"> {{ number_format($support->goal, 0, ',', '.') }} </td>
                                @if ($support->total > 0 && $support->goal > 0)
                                    <td class="border">
                                        {{ number_format($support->total / ($support->goal / 100), 2, ',', ' ') }}%
                                    </td>
                                @else
                                    <td class="border"> 0% </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <thead>
                            <tr>
                                <th class="border">Nome</th>
                                <th class="border">Cães</th>
                                <th class="border">Cadelas</th>
                                <th class="border">Total Cães</th>
                                <th class="border">Gatos</th>
                                <th class="border">Gatas</th>
                                <th class="border">Total Gatos</th>
                                <th class="border">Total</th>
                                <th class="border">Meta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($support->points as $point)
                                <tr>
                                    <td class="border"> {{ $point->point->name }} </td>

                                    <td class="border"> {{ number_format($point->male_dogs, 0, ',', '.') }} </td>

                                    <td class="border"> {{ number_format($point->female_dogs, 0, ',', '.') }} </td>

                                    <td class="border"> {{ number_format($point->total_of_dogs, 0, ',', '.') }} </td>

                                    <td class="border"> {{ number_format($point->male_cat, 0, ',', '.') }} </td>

                                    <td class="border"> {{ number_format($point->female_cat, 0, ',', '.') }} </td>

                                    <td class="border"> {{ number_format($point->total_of_cats, 0, ',', '.') }} </td>

                                    <td class="border"> {{ number_format($point->total, 0, ',', '.') }} </td>

                                    <td class="border"> {{ number_format($point->goal, 0, ',', '.') }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

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
