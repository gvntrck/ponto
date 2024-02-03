<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de ponto</title>
    <link rel="manifest" href="manifest.json">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php
    include 'header.php';
    require_once('../wp-load.php');
    require_once('verificaAcesso.php');
    


    ?>
</head>

<body>
    <div class="container">
        <h2>Registro de Ponto 1.1.1</h2>
        <form class="form-horizontal" id="pontoForm">
            <div class="form-group">
                <label for="comentario">Comentário:</label>
                <textarea id="comentario" class="form-control" name="comentario"></textarea>
            </div>
            <div class="botoes_envia_e_relatorio">
                <button class="btn btn-primary" type="submit">Registrar</button>
            </div>
        </form>
        <div id="message"></div>
    </div>

    <script>
        $(document).ready(function () {
            $("#pontoForm").submit(function (event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'salvarDados.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        var cleanResponse = response.replace(/<script[^>]*>([\S\s]*?)<\/script>/gi, '');
                        Swal.fire({
                            title: "Salvo!",
                            text: cleanResponse,
                            icon: "success"
                        });
                        $('#message').html(response);
                        $('#pontoForm').trigger("reset");
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            title: "Erro!",
                            text: "Ocorreu um erro ao salvar os dados: " + error,
                            icon: "error"
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>