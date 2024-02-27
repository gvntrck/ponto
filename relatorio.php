<?php
include 'header.php';
require_once('../wp-config.php');
require_once('../wp-load.php');
require_once('verificaAcesso.php');



// Obtem o ID do usuário logado
$current_user_id = get_current_user_id();

// Busca anos disponíveis
$queryAnos = "SELECT DISTINCT YEAR(data) AS ano FROM {$wpdb->prefix}ponto WHERE user_id = '{$current_user_id}' ORDER BY ano DESC";
$anos = $wpdb->get_results($queryAnos);

// Define ano e mês selecionados (padrão: ano e mês atual)
$anoSelecionado = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$mesSelecionado = isset($_GET['mes']) ? $_GET['mes'] : date('m');

// Busca meses disponíveis no ano selecionado
$queryMeses = "SELECT DISTINCT MONTH(data) AS mes FROM {$wpdb->prefix}ponto WHERE YEAR(data) = '{$anoSelecionado}' AND user_id = '{$current_user_id}' ORDER BY mes DESC";
$meses = $wpdb->get_results($queryMeses);

// Filtra resultados com base no ano e mês selecionados
$query = "SELECT * FROM {$wpdb->prefix}ponto WHERE YEAR(data) = '{$anoSelecionado}' AND MONTH(data) = '{$mesSelecionado}' AND user_id = '{$current_user_id}' ORDER BY data DESC";
$resultados = $wpdb->get_results($query);


function traduzirDiaDaSemana($diaEmIngles)
{
    $dias = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda-feira',
        'Tuesday' => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday' => 'Quinta-feira',
        'Friday' => 'Sexta-feira',
        'Saturday' => 'Sábado'
    ];
    return $dias[$diaEmIngles] ?? $diaEmIngles;
}





function calcularTotalHorasDia($entrada, $saida, $entradaAlmoco, $retornoAlmoco, $entradaExtra, $saidaExtra)
{
    $totalHoras = 0;

    // Calcula as horas trabalhadas
    if (!empty($entrada) && !empty($saida)) {
        $inicio = strtotime($entrada);
        $fim = strtotime($saida);
        $totalHoras = $fim - $inicio;
    }

    // Subtrai intervalo de almoço
    if (!empty($entradaAlmoco) && !empty($retornoAlmoco)) {
        $inicioAlmoco = strtotime($entradaAlmoco);
        $fimAlmoco = strtotime($retornoAlmoco);
        $totalAlmoco = $fimAlmoco - $inicioAlmoco;
        $totalHoras -= $totalAlmoco;
    }

    // Adiciona horas extras
    if (!empty($entradaExtra) && !empty($saidaExtra)) {
        $inicioExtra = strtotime($entradaExtra);
        $fimExtra = strtotime($saidaExtra);
        $totalExtra = $fimExtra - $inicioExtra;
        $totalHoras += $totalExtra;
    }

    // Garante que o total de horas não seja negativo
    if ($totalHoras < 0) {
        $totalHoras = 0;
    }

    // Converte de volta para o formato H:i
    return sprintf('%02d:%02d', ($totalHoras / 3600), ($totalHoras / 60) % 60);
}



function calcularTotalExtras($entrada, $saida, $entradaAlmoco, $retornoAlmoco, $entradaExtra, $saidaExtra)
{
    $totalHorasDia = calcularTotalHorasDia($entrada, $saida, $entradaAlmoco, $retornoAlmoco, $entradaExtra, $saidaExtra);
    list($horas, $minutos) = explode(':', $totalHorasDia);
    $totalMinutosDia = $horas * 60 + $minutos;

    $oitoHorasEmMinutos = 8 * 60;
    $extrasEmMinutos = $totalMinutosDia - $oitoHorasEmMinutos;

    if ($extrasEmMinutos > 0) {
        return sprintf('%02d:%02d', ($extrasEmMinutos / 60), $extrasEmMinutos % 60);
    }

    return '00:00';
}
?>

<script>
    function refreshPage() {
        window.location.reload();
    }
</script>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Ponto</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <form action="" method="GET">
                <select name="ano" onchange="this.form.submit()">
                    <?php foreach ($anos as $ano): ?>
                        <option value="<?= $ano->ano ?>" <?= $ano->ano == $anoSelecionado ? 'selected' : '' ?>>
                            <?= $ano->ano ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="mes" onchange="this.form.submit()">
                    <?php foreach ($meses as $mes): ?>
                        <option value="<?= $mes->mes ?>" <?= $mes->mes == $mesSelecionado ? 'selected' : '' ?>>
                            <?= sprintf('%02d', $mes->mes) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-primary ml-3" onclick="refreshPage()">Atualizar</button>

            </form>



            <thead>
                <h3>Relatório de Ponto</h3>
                <tr>

                    <th>Data</th>
                    <th>Dia da Semana</th>
                    <th>Entrada</th>
                    <th>Entrada Almoço</th>
                    <th>Retorno Almoço</th>
                    <th>Saída</th>
                    <th>Entrada Extra</th>
                    <th>Saída Extra</th>
                    <th>Total Extras</th>
                    <th>Total Horas Dia</th>
                    <th>Obs</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $registro): ?>
                    <tr>
                        <td style="white-space: nowrap;">
                            <?= $registro->data ?>
                        </td>
                        <td>
                            <?= traduzirDiaDaSemana(date('l', strtotime($registro->data))) ?>
                        </td>
                        <td>
                            <?= $registro->entrada ?>
                        </td>
                        <td>
                            <?= $registro->entradaAlmoco ?>
                        </td>
                        <td>
                            <?= $registro->retornoAlmoco ?>
                        </td>
                        <td>
                            <?= $registro->saida ?>
                        </td>
                        <td>
                            <?= $registro->entradaExtra ?>
                        </td>
                        <td>
                            <?= $registro->saidaExtra ?>
                        </td>
                        <td>
                            <?php
                            // Chama a função calcularTotalExtras com os campos apropriados
                            echo calcularTotalExtras(
                                $registro->entrada,
                                $registro->saida,
                                $registro->entradaAlmoco,
                                $registro->retornoAlmoco,
                                $registro->entradaExtra,
                                $registro->saidaExtra
                            );
                            ?>
                        </td>
                        <td>
                            <?php
                            // Chama a função calcularTotalHorasDia com os campos apropriados
                            echo calcularTotalHorasDia(
                                $registro->entrada,
                                $registro->saida,
                                $registro->entradaAlmoco,
                                $registro->retornoAlmoco,
                                $registro->entradaExtra,
                                $registro->saidaExtra
                            );
                            ?>
                        </td>
                        <td>
                            <?= $registro->obs ?>
                        </td>
                        <td><a href="editar.php?id=<?= $registro->id ?>">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</body>

</html>