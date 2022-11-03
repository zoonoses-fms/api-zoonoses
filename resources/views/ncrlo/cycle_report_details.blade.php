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
                <h2>Relatório de Vicinação</h2>
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
                        <th class="border" colspan="6">Cão</th>
                        <th class="border" colspan="6">Cadela</th>
                        <th class="border">Geral</th>
                    </tr>

                    <tr>
                        <th class="border">&lt;4M</th>
                        <th class="border">4M-&lt;1A</th>
                        <th class="border">1A-&lt;2A</th>
                        <th class="border">2A-&lt;4A</th>
                        <th class="border">&gt;4M</th>
                        <th class="border">Total</th>
                        <th class="border">&lt;4A</th>
                        <th class="border">4M-&lt;1A</th>
                        <th class="border">1A-&lt;2A</th>
                        <th class="border">2A-&lt;4A</th>
                        <th class="border">&gt;4A</th>
                        <th class="border">Total</th>
                        <th class="border">Total</th>
                    </tr>

                </thead>
                <tbody>
                    <tr>
                        <td class="border"> {{ $cycle->male_dog_under_4m }} </td>
                        <td class="border"> {{ $cycle->male_dog_major_4m_under_1y }} </td>
                        <td class="border"> {{ $cycle->male_dog_major_1y_under_2y }} </td>
                        <td class="border"> {{ $cycle->male_dog_major_2y_under_4y }} </td>
                        <td class="border"> {{ $cycle->male_dog_major_4y }} </td>
                        <td class="border"> {{ $cycle->male_dogs }} </td>
                        <td class="border"> {{ $cycle->female_dog_under_4m }} </td>
                        <td class="border"> {{ $cycle->female_dog_major_4m_under_1y }} </td>
                        <td class="border"> {{ $cycle->female_dog_major_1y_under_2y }} </td>
                        <td class="border"> {{ $cycle->female_dog_major_2y_under_4y }} </td>
                        <td class="border"> {{ $cycle->female_dog_major_4y }} </td>
                        <td class="border"> {{ $cycle->female_dogs }} </td>
                        <td class="border"> {{ $cycle->total_of_dogs }} </td>
                    </tr>
                </tbody>
            </table>

            @if (count($cycle->supports) > 0)
                @foreach ($cycle->supports as $support)
                    <div>
                        <h4> {{ $support->support->name }} </h4>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th class="border" colspan="6">Cão</th>
                                <th class="border" colspan="6">Cadela</th>
                                <th class="border">Geral</th>
                            </tr>

                            <tr>
                                <th class="border">&lt;4M</th>
                                <th class="border">4M-&lt;1A</th>
                                <th class="border">1A-&lt;2A</th>
                                <th class="border">2A-&lt;4A</th>
                                <th class="border">&gt;4A</th>
                                <th class="border">Total</th>
                                <th class="border">&lt;4M</th>
                                <th class="border">4M-&lt;1A</th>
                                <th class="border">1A-&lt;2A</th>
                                <th class="border">2A-&lt;4A</th>
                                <th class="border">&gt;4A</th>
                                <th class="border">Total</th>
                                <th class="border">Total</th>
                            </tr>

                        </thead>
                        <tbody>
                            <tr>
                                <td class="border"> {{ $support->male_dog_under_4m }} </td>
                                <td class="border"> {{ $support->male_dog_major_4m_under_1y }} </td>
                                <td class="border"> {{ $support->male_dog_major_1y_under_2y }} </td>
                                <td class="border"> {{ $support->male_dog_major_2y_under_4y }} </td>
                                <td class="border"> {{ $support->male_dog_major_4y }} </td>
                                <td class="border"> {{ $support->male_dogs }} </td>
                                <td class="border"> {{ $support->female_dog_under_4m }} </td>
                                <td class="border"> {{ $support->female_dog_major_4m_under_1y }} </td>
                                <td class="border"> {{ $support->female_dog_major_1y_under_2y }} </td>
                                <td class="border"> {{ $support->female_dog_major_2y_under_4y }} </td>
                                <td class="border"> {{ $support->female_dog_major_4y }} </td>
                                <td class="border"> {{ $support->female_dogs }} </td>
                                <td class="border"> {{ $support->total_of_dogs }} </td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th class="border" colspan="6">Cão</th>
                                <th class="border" colspan="6">Cadela</th>
                                <th class="border">Geral</th>
                            </tr>

                            <tr>
                                <th class="border">Nome</th>
                                <th class="border">&lt;4M</th>
                                <th class="border">4M-&lt;1A</th>
                                <th class="border">1A-&lt;2A</th>
                                <th class="border">2A-&lt;4A</th>
                                <th class="border">&gt;4A</th>
                                <th class="border">Total</th>
                                <th class="border">&lt;4M</th>
                                <th class="border">4M-&lt;1A</th>
                                <th class="border">1A-&lt;2A</th>
                                <th class="border">2A-&lt;4A</th>
                                <th class="border">&gt;4A</th>
                                <th class="border">Total</th>
                                <th class="border">Total</th>
                            </tr>

                        </thead>
                        @foreach ($support->points as $point)
                        <tbody>
                            <tr>
                                <td class="border"> {{ $point->point->name }} </td>
                                <td class="border"> {{ $point->male_dog_under_4m }} </td>
                                <td class="border"> {{ $point->male_dog_major_4m_under_1y }} </td>
                                <td class="border"> {{ $point->male_dog_major_1y_under_2y }} </td>
                                <td class="border"> {{ $point->male_dog_major_2y_under_4y }} </td>
                                <td class="border"> {{ $point->male_dog_major_4y }} </td>
                                <td class="border"> {{ $point->male_dogs }} </td>
                                <td class="border"> {{ $point->female_dog_under_4m }} </td>
                                <td class="border"> {{ $point->female_dog_major_4m_under_1y }} </td>
                                <td class="border"> {{ $point->female_dog_major_1y_under_2y }} </td>
                                <td class="border"> {{ $point->female_dog_major_2y_under_4y }} </td>
                                <td class="border"> {{ $point->female_dog_major_4y }} </td>
                                <td class="border"> {{ $point->female_dogs }} </td>
                                <td class="border"> {{ $point->total_of_dogs }} </td>
                            </tr>
                        </tbody>
                        @endforeach
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
