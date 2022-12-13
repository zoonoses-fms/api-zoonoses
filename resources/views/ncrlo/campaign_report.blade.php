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
            font-family: Arial, Helvetica, sans-serif;
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
                <h3>{{ $campaign->year }} de {{ date('d-m-Y', strtotime($campaign->start)) }} até
                    {{ date('d-m-Y', strtotime($campaign->end)) }} </h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="border">Cães</th>
                        <th class="border">% Cães</th>
                        <th class="border">Cadelas</th>
                        <th class="border">% Cadelas</th>
                        <th class="border">Total Cães</th>
                        <th class="border">% Total Cães</th>
                        <th class="border">Gatos</th>
                        <th class="border">% Gatos</th>
                        <th class="border">Gatas</th>
                        <th class="border">% Gatas</th>
                        <th class="border">Total Gatos</th>
                        <th class="border">% Total Gatos</th>
                        <th class="border">Total</th>
                        <th class="border">Meta</th>
                        <th class="border">Cobertura</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border"> {{ number_format($campaign->male_dogs, 0, ',', ' ') }} </td>
                        @if ($campaign->male_dogs > 0 && $campaign->total_of_dogs > 0)
                            <td class="border">
                                {{ number_format($campaign->male_dogs / ($campaign->total_of_dogs / 100), 2, ',', ' ') }}%
                            </td>
                        @else
                            <td class="border"> 0% </td>
                        @endif
                        <td class="border"> {{ number_format($campaign->female_dogs, 0, ',', ' ') }} </td>
                        @if ($campaign->female_dogs > 0 && $campaign->total_of_dogs > 0)
                            <td class="border">
                                {{ number_format($campaign->female_dogs / ($campaign->total_of_dogs / 100), 2, ',', ' ') }}%
                            </td>
                        @else
                            <td class="border"> 0% </td>
                        @endif
                        <td class="border"> {{ number_format($campaign->total_of_dogs, 0, ',', ' ') }} </td>
                        @if ($campaign->total > 0 && $campaign->total_of_dogs > 0)
                            <td class="border">
                                {{ number_format($campaign->total_of_dogs / ($campaign->total / 100), 2, ',', ' ') }}%
                            </td>
                        @else
                            <td class="border"> 0% </td>
                        @endif
                        <td class="border"> {{ number_format($campaign->male_cat, 0, ',', ' ') }} </td>
                        @if ($campaign->male_cat > 0 && $campaign->total_of_cats > 0)
                            <td class="border">
                                {{ number_format($campaign->male_cat / ($campaign->total_of_cats / 100), 2, ',', ' ') }}%
                            </td>
                        @else
                            <td class="border"> 0% </td>
                        @endif
                        <td class="border"> {{ number_format($campaign->female_cat, 0, ',', ' ') }} </td>
                        @if ($campaign->female_cat > 0 && $campaign->total_of_cats > 0)
                            <td class="border">
                                {{ number_format($campaign->female_cat / ($campaign->total_of_cats / 100), 2, ',', ' ') }}%
                            </td>
                        @else
                            <td class="border"> 0% </td>
                        @endif
                        <td class="border"> {{ number_format($campaign->total_of_cats, 0, ',', ' ') }} </td>
                        @if ($campaign->total > 0 && $campaign->total_of_cats > 0)
                            <td class="border">
                                {{ number_format($campaign->total_of_cats / ($campaign->total / 100), 2, ',', ' ') }}%
                            </td>
                        @else
                            <td class="border"> 0% </td>
                        @endif
                        <td class="border"> {{ number_format($campaign->total, 0, ',', ' ') }} </td>
                        <td class="border"> {{ number_format($campaign->goal, 0, ',', ' ') }} </td>
                        @if ($campaign->total > 0 && $campaign->goal > 0)
                            <td class="border">
                                {{ number_format($campaign->total / ($campaign->goal / 100), 2, ',', ' ') }}%
                            </td>
                        @else
                            <td class="border"> 0% </td>
                        @endif
                    </tr>
                </tbody>
            </table>

            @if (count($campaign->cycles) > 0)
                <table>
                    <thead>
                        <tr>
                            <th class="border">Etapa</th>
                            <th class="border">Cães</th>
                            <th class="border">% Cães</th>
                            <th class="border">Cadelas</th>
                            <th class="border">% Cadelas</th>
                            <th class="border">Total Cães</th>
                            <th class="border">% Total Cães</th>
                            <th class="border">Gatos</th>
                            <th class="border">% Gatos</th>
                            <th class="border">Gatas</th>
                            <th class="border">% Gatas</th>
                            <th class="border">Total Gatos</th>
                            <th class="border">% Total Gatos</th>
                            <th class="border">Total</th>
                            <th class="border">Meta</th>
                            <th class="border">Cobertura</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($campaign->cycles as $cycle)
                            <tr>
                                <td class="border"> {{ $cycle->description }} </td>
                                <td class="border"> {{ number_format($cycle->male_dogs, 0, ',', ' ') }} </td>
                                @if ($cycle->male_dogs > 0 && $cycle->total_of_dogs > 0)
                                    <td class="border">
                                        {{ number_format($cycle->male_dogs / ($cycle->total_of_dogs / 100), 2, ',', ' ') }}%
                                    </td>
                                @else
                                    <td class="border"> 0% </td>
                                @endif
                                <td class="border"> {{ number_format($cycle->female_dogs, 0, ',', ' ') }} </td>
                                @if ($cycle->female_dogs > 0 && $cycle->total_of_dogs > 0)
                                    <td class="border">
                                        {{ number_format($cycle->female_dogs / ($cycle->total_of_dogs / 100), 2, ',', ' ') }}%
                                    </td>
                                @else
                                    <td class="border"> 0% </td>
                                @endif
                                <td class="border"> {{ number_format($cycle->total_of_dogs, 0, ',', ' ') }} </td>
                                @if ($cycle->total > 0 && $cycle->total_of_dogs > 0)
                                    <td class="border">
                                        {{ number_format($cycle->total_of_dogs / ($cycle->total / 100), 2, ',', ' ') }}%
                                    </td>
                                @else
                                    <td class="border"> 0% </td>
                                @endif
                                <td class="border"> {{ number_format($cycle->male_cat, 0, ',', ' ') }} </td>
                                @if ($cycle->male_cat > 0 && $cycle->total_of_cats > 0)
                                    <td class="border">
                                        {{ number_format($cycle->male_cat / ($cycle->total_of_cats / 100), 2, ',', ' ') }}%
                                    </td>
                                @else
                                    <td class="border"> 0% </td>
                                @endif
                                <td class="border"> {{ number_format($cycle->female_cat, 0, ',', ' ') }} </td>
                                @if ($cycle->female_cat > 0 && $cycle->total_of_cats > 0)
                                    <td class="border">
                                        {{ number_format($cycle->female_cat / ($cycle->total_of_cats / 100), 2, ',', ' ') }}%
                                    </td>
                                @else
                                    <td class="border"> 0% </td>
                                @endif
                                <td class="border"> {{ number_format($cycle->total_of_cats, 0, ',', ' ') }} </td>
                                @if ($cycle->total > 0 && $cycle->total_of_cats > 0)
                                    <td class="border">
                                        {{ number_format($cycle->total_of_cats / ($cycle->total / 100), 2, ',', ' ') }}%
                                    </td>
                                @else
                                    <td class="border"> 0% </td>
                                @endif
                                <td class="border"> {{ number_format($cycle->total, 0, ',', ' ') }} </td>
                                <td class="border"> {{ number_format($cycle->goal, 0, ',', ' ') }} </td>
                                @if ($cycle->total > 0 && $cycle->goal > 0)
                                    <td class="border">
                                        {{ number_format($cycle->total / ($cycle->goal / 100), 2, ',', ' ') }}%
                                    </td>
                                @else
                                    <td class="border"> 0% </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
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
