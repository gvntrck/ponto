<?php
include 'header.php';
require_once('../wp-config.php');
require_once('../wp-load.php');
 require_once('verificaAcesso.php');

global $wpdb;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar dados do registro
$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}ponto WHERE id = %d", $id);
$registro = $wpdb->get_row($query);

if (!$registro) {
    echo "Registro não encontrado!";
    exit;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dadosAtualizados = [];

    foreach (['entrada', 'entradaAlmoco', 'retornoAlmoco', 'saida', 'entradaExtra', 'saidaExtra', 'obs'] as $campo) {
        if (!empty($_POST[$campo])) {
            $dadosAtualizados[$campo] = $_POST[$campo];
        } else {
            // Se o campo estiver vazio, atribuir NULL
            $dadosAtualizados[$campo] = NULL;
        }
    }

    if (!empty($dadosAtualizados)) {
        $wpdb->update("{$wpdb->prefix}ponto", $dadosAtualizados, ['id' => $id]);
    }

    wp_redirect('/ponto/relatorio.php');
    exit;
}

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <!-- Importe o CSS do Bootstrap usando o CDN -->

</head>

<body>
    <div class="container">
        <h1>Editar Registro</h1>
        <form method="post">
            <div class="mb-3">
                <label for="entrada" class="form-label">Entrada:</label>
                <input type="text" class="form-control" id="entrada" name="entrada" value="<?= $registro->entrada ?>">
            </div>
            <div class="mb-3">
                <label for="entradaAlmoco" class="form-label">Entrada Almoço:</label>
                <input type="text" class="form-control" id="entradaAlmoco" name="entradaAlmoco"
                    value="<?= $registro->entradaAlmoco ?>">
            </div>
            <div class="mb-3">
                <label for="retornoAlmoco" class="form-label">Retorno Almoço:</label>
                <input type="text" class="form-control" id="retornoAlmoco" name="retornoAlmoco"
                    value="<?= $registro->retornoAlmoco ?>">
            </div>
            <div class="mb-3">
                <label for="saida" class="form-label">Saída:</label>
                <input type="text" class="form-control" id="saida" name="saida" value="<?= $registro->saida ?>">
            </div>
            <div class="mb-3">
                <label for="entradaExtra" class="form-label">Entrada Extra:</label>
                <input type="text" class="form-control" id="entradaExtra" name="entradaExtra"
                    value="<?= $registro->entradaExtra ?>">
            </div>
            <div class="mb-3">
                <label for="saidaExtra" class="form-label">Saída Extra:</label>
                <input type="text" class="form-control" id="saidaExtra" name="saidaExtra"
                    value="<?= $registro->saidaExtra ?>">
            </div>
            <div class="mb-3">
                <label for="obs" class="form-label">Observações:</label>
                <textarea class="form-control" id="obs" name="obs"><?= $registro->obs ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>

    <!-- Importe o JavaScript do Bootstrap usando o CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.min.js"></script>
</body>

</html>