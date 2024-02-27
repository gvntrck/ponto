/**
 * Verifica se o usuário tem acesso ao sistema especificado.
 *
 * @param string $sistema O nome do sistema a ser verificado.
 * @return bool Retorna true se o usuário tiver permissão de acesso, caso contrário redireciona para a página 'sem acesso'.
 */
function verificar_acesso($sistema) {
    // ...
}
<?php
require_once('../wp-load.php');

function verificar_acesso($sistema) {
    session_start();

    // Verifica se o usuário está logado.
    if (!is_user_logged_in()) {
        wp_redirect(home_url('/ponto/acesso.php'));
        exit;
    }

    $current_user = wp_get_current_user();
    $nome_usuario = $current_user->user_login;

    // Verifica se as permissões já estão armazenadas na sessão.
    if (!isset($_SESSION['permissao_'.$sistema])) {
        // Se não estiverem na sessão, busca no banco de dados.
        global $wpdb;
        $_SESSION['permissao_'.$sistema] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM wp_sistemas_permissoes WHERE nome_usuario = %s AND sistema = %s",
                $nome_usuario,
                $sistema
            )
        ) > 0;
    }

    // Verifica se o usuário tem permissão.
    if (!$_SESSION['permissao_'.$sistema]) {
        // Se não tiver permissão, redireciona para a página 'sem acesso'.
        $url_sem_acesso = home_url('/sistemas/semacesso');
        if ($_SERVER['REQUEST_URI'] !== $url_sem_acesso) {
            wp_redirect($url_sem_acesso);
            exit;
        }
    } else {
        // Se tiver permissão, permite o acesso normal à página.
        return true;
    }
}

// Chama a função de verificação para o sistema desejado.
verificar_acesso('ponto');

