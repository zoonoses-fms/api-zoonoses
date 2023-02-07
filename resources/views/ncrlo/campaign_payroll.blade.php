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

        .profile {
            width: 20%;
        }

        .value {
            width: 10%;
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
                <h3>Folha de pagamento - {{ $campaign->year }} </h3>
            </div>
        </div>

        <div class="content">

            <table class="table-vacination">
                <thead>
                    <th class="border">Perfil</th>
                    <th class="border">Quantidade</th>
                    <th class="border">Valor</th>
                    <th class="border">Total</th>
                </thead>
                <tbody>
                    @for ($i = 0; $i < count($listProfile); $i++)
                        <tr>
                            <td class="border">{{ $listProfile[$i]->profile }} - {{ $listProfile[$i]->management }}
                            </td>
                            <td class="border">{{ $listProfile[$i]->count }} </td>
                            <td class="border">R$ {{ $listProfile[$i]->cost }} </td>
                            <td class="border">R$ {{ number_format($listProfile[$i]->total, 2, ',', '.') }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            <p></p>
            <table class="table-vacination">
                <thead>
                    <th class="border">Etapa</th>
                    <th class="border">Valor</th>
                </thead>
                <tbody>
                    @foreach ($campaign->cycles as $cycle)
                        <tr>
                            <td class="border">{{ $cycle->description }} </td>
                            <td class="border">R$ {{ number_format($cycle->total, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p></p>
            <table class="table-vacination">
                <tbody>
                    <tr>
                        <td class="border">Total {{ $campaign->year }} </td>
                        <td class="border">R$ {{ number_format($total, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
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
