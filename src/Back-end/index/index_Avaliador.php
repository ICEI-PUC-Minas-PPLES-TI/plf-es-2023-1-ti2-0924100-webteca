<?php
session_start();
if((!isset($_SESSION['Nome_Avaliador']) == true) 
and (!isset($_SESSION['Senha_Avaliador']) == true)){
    //Caso o nome e/ou a senha estejam faltando;
    unset($_SESSION['Nome_Avaliador']);
    unset($_SESSION['Senha_Avaliador']);
    header("Location: login_do_avaliador.html");
}
$logado = $_SESSION['Nome_Avaliador'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <title>Webteca-Avaliador</title>
</head>

<body>
    <div class="container">
        <!-- nav -->
        <nav>
            <div class="logo">
                <div class="img-logo3">
                <img src="imagens/logo3.png">
                </div>
            </div>
            <ul>
                <li><a href="indicadores.php">Indicadores de desempenho</a></li>
                <li><a href="colocar_narrador.php">Colocar Narrador</a></li>
                <li><a href="remover_livro.php">Remover Livro</a></li>
                <button class="btn"><a href="sair_Avaliador.php">Sair</a></button>
            </ul>
            <div class="menu-icon">
                <img src="imagens/menu.png">
            </div>
        </nav>
        <!-- end nav -->
    </div>
    <script src="js/main.js"></script>
</body>
</html>