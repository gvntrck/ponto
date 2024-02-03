<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
require_once('../wp-config.php');
require_once('../wp-load.php');

if (!is_user_logged_in()) {
    $redirect_url = urlencode($_SERVER['REQUEST_URI']);
    wp_redirect(wp_login_url($redirect_url));
    exit;
}

global $wpdb;
$wpdb->query("SET time_zone = 'America/Sao_Paulo'");

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

$diaDaSemana = traduzirDiaDaSemana(date('l'));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';
    $current_user_id = get_current_user_id();
    $data_atual = date('Y-m-d');

    $timezone = new DateTimeZone('America/Sao_Paulo');
    $now = new DateTime('now', $timezone);
    $offset = $timezone->getOffset($now);
    $hora_atual = gmdate('H:i:s', time() + $offset);

    $table_name = $wpdb->prefix . 'ponto';
    $registro_existente = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND data = %s", $current_user_id, $data_atual));

    if (is_null($registro_existente)) {
        $sql = $wpdb->prepare("INSERT INTO $table_name (data, entrada, user_id, obs, diaSemana) VALUES (%s, %s, %d, %s, %s)", $data_atual, $hora_atual, $current_user_id, $comentario, $diaDaSemana);
    } else {
        // Adiciona os novos campos no array
        $campos = ['entradaAlmoco', 'retornoAlmoco', 'saida', 'entradaExtra', 'saidaExtra'];
        $campo_atualizar = null;
        foreach ($campos as $campo) {
            if (is_null($registro_existente->$campo)) {
                $campo_atualizar = $campo;
                break;
            }
        }

        if ($campo_atualizar) {
            $novo_comentario = $registro_existente->obs . ' | ' . $comentario;
            $sql = $wpdb->prepare("UPDATE $table_name SET $campo_atualizar = %s, obs = %s, diaSemana = %s WHERE user_id = %d AND data = %s", $hora_atual, $novo_comentario, $diaDaSemana, $current_user_id, $data_atual);
        } else {
            echo 'Erro: Todas as marcações de hoje já foram feitas.';
            exit;
        }
    }

    $result = $wpdb->query($sql);
    if ($result !== false) {
        if (is_null($registro_existente)) {
            echo 'Entrada salva com sucesso!';
        } elseif ($campo_atualizar == 'entradaAlmoco') {
            echo 'Entrada do almoço salva com sucesso!';
        } elseif ($campo_atualizar == 'retornoAlmoco') {
            echo 'Retorno do almoço salvo com sucesso!';
        } elseif ($campo_atualizar == 'saida') {
            echo 'Saída salva com sucesso!';
        } elseif ($campo_atualizar == 'entradaExtra') {
            echo 'Entrada extra salva com sucesso!';
        } elseif ($campo_atualizar == 'saidaExtra') {
            echo 'Saída extra salva com sucesso!';
        } else {
            echo 'Ação salva com sucesso!';
        }
    } else {
        echo 'Erro ao salvar os dados. Detalhe do erro: ' . $wpdb->last_error;
    }



} else {
    echo 'Método de requisição inválido.';
}
?>