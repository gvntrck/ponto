<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de ponto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">



</head>




<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Logo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">

                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Início <span class="sr-only">(atual)</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Registrar ponto</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="relatorio.php">Relatorio</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>



                <li class="nav-item">
                    <?php
                    require_once('../wp-load.php');
                    $current_user = wp_get_current_user();
                    $user_id = $current_user->ID;
                    $user_data = get_userdata($user_id);


                    ?>
                    <a class="nav-link">
                        Bem-vindo:

                        <strong>
                            <?php echo esc_html($current_user->display_name); ?>
                        </strong>

                    </a>

                </li>





            </ul>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>