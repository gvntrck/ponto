/**
 * FILEPATH: /gvntrck/ponto/dashboard.php
 *
 * Este arquivo representa a página do painel de controle do aplicativo.
 * Ele exibe o total de horas extras para o mês e a semana atual.
 * Os cálculos são baseados nos registros de tempo do usuário armazenados no banco de dados.
 *
 * As seguintes funções são definidas neste arquivo:
 *
 * - calcularTotalHorasDia(): Calcula o total de horas trabalhadas em um dia, levando em consideração o intervalo de almoço e horas extras.
 * - calcularTotalExtras(): Calcula o total de horas extras em um dia.
 *
 * O arquivo também inclui as dependências necessárias e realiza consultas ao banco de dados para recuperar os registros de tempo.
 * Os dados recuperados são usados para calcular o total de horas extras para o mês e a semana.
 */
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Dashboard</title>
    <link rel="manifest" href="manifest.json">
    <!--   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">   -->


    <?php
    include 'header.php';
    require_once('../wp-load.php');
    require_once('verificaAcesso.php');

    global $wpdb;
    $current_user_id = get_current_user_id();

    // Funções de cálculo de horas extras
    function calcularTotalHorasDia($entrada, $saida, $entradaAlmoco, $retornoAlmoco, $entradaExtra, $saidaExtra)
    {
        $inicio = strtotime($entrada);
        $fim = strtotime($saida);
        $totalHoras = $fim - $inicio;

        if (!empty($entradaAlmoco) && !empty($retornoAlmoco)) {
            $inicioAlmoco = strtotime($entradaAlmoco);
            $fimAlmoco = strtotime($retornoAlmoco);
            $totalAlmoco = $fimAlmoco - $inicioAlmoco;
            $totalHoras -= $totalAlmoco;
        }

        if (!empty($entradaExtra) && !empty($saidaExtra)) {
            $inicioExtra = strtotime($entradaExtra);
            $fimExtra = strtotime($saidaExtra);
            $totalExtra = $fimExtra - $inicioExtra;
            $totalHoras += $totalExtra;
        }

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

    // Consultas para horas extras do mês e da semana
    $mesAtual = date('Y-m');
    $inicioSemana = date('Y-m-d', strtotime('last monday'));
    $fimSemana = date('Y-m-d', strtotime('next sunday'));

    $queryMes = "SELECT entrada, saida, entradaAlmoco, retornoAlmoco, entradaExtra, saidaExtra FROM {$wpdb->prefix}ponto WHERE data LIKE '{$mesAtual}%' AND user_id = $current_user_id";
    $resultadosMes = $wpdb->get_results($queryMes);

    $querySemana = "SELECT entrada, saida, entradaAlmoco, retornoAlmoco, entradaExtra, saidaExtra FROM {$wpdb->prefix}ponto WHERE data BETWEEN '{$inicioSemana}' AND '{$fimSemana}' AND user_id = $current_user_id";
    $resultadosSemana = $wpdb->get_results($querySemana);

    $totalHorasExtrasMes = 0;
    foreach ($resultadosMes as $registro) {
        list($horas, $minutos) = explode(
            ':',
            calcularTotalExtras(
                $registro->entrada,
                $registro->saida,
                $registro->entradaAlmoco,
                $registro->retornoAlmoco,
                $registro->entradaExtra,
                $registro->saidaExtra
            )
        );
        $totalHorasExtrasMes += ($horas * 60 + $minutos);
    }
    $totalHorasExtrasMes = sprintf('%02d:%02d', ($totalHorasExtrasMes / 60), $totalHorasExtrasMes % 60);

    $totalHorasExtrasSemana = 0;
    foreach ($resultadosSemana as $registro) {
        list($horas, $minutos) = explode(
            ':',
            calcularTotalExtras(
                $registro->entrada,
                $registro->saida,
                $registro->entradaAlmoco,
                $registro->retornoAlmoco,
                $registro->entradaExtra,
                $registro->saidaExtra
            )
        );
        $totalHorasExtrasSemana += ($horas * 60 + $minutos);
    }
    $totalHorasExtrasSemana = sprintf('%02d:%02d', ($totalHorasExtrasSemana / 60), $totalHorasExtrasSemana % 60);
    ?>
</head>

<body>
    <div class="container">
        <h2 class="my-4">Dashboard</h2>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total de horas extras esse mês</h5>
                        <p class="card-text"><strong>
                                <?php echo $totalHorasExtrasMes; ?>
                            </strong></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total de horas extras essa semana</h5>
                        <p class="card-text"><strong>
                                <?php echo $totalHorasExtrasSemana; ?>
                            </strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>