<?php
session_start();
if((!isset($_SESSION['Nome_Leitor']) == true) 
and (!isset($_SESSION['Senha_Leitor']) == true)){
    //Caso o nome e/ou a senha estejam faltando;
    unset($_SESSION['Nome_Leitor']);
    unset($_SESSION['Senha_Leitor']);
    header("Location: login.html");
}
$logado = $_SESSION['Nome_Leitor'];
include_once('config.php');
$nomeUsuario = $_SESSION['Nome_Leitor'];
$query = "SELECT * FROM leitores WHERE Nome_Leitor = '$nomeUsuario'";
$result = mysqli_query($conexao, $query);
$row = mysqli_fetch_assoc($result);
$CPF = $row['CPF_Leitor'];
$nome = $row['Nome_Leitor'];
$email = $row['Email_Leitor'];
$senha = $row['Senha_Leitor'];
$pagamento = $row['Pagamento'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <title>Webteca-Leitor</title>
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
                <li><a href="index_Leitor.php">Home</a></li>
                <li><a href="notificacao.php">Notificações</a></li>
                <li><a href="credito.html?CPF=<?php echo $CPF; ?>&nome=<?php echo $nome; ?>&email=<?php echo $email; ?>&senha=<?php echo $senha; ?>&pagamento=<?php echo $pagamento; ?>">Conta Premium</a></li>
                <li><a href="virar_narrador.html">Virar Narrador</a></li>
                <li><a href="postar_livro.php">Postar Livro</a></li>
                <li><a href="pedir_livro.html">Pedir Livro</a></li>
                <li><a href="pedir_remocaolivro.html">Remover Livro</a></li>
                <button class="btn"><a href="sair_Leitor.php">Sair</a></button>
            </ul>
            <div class="menu-icon">
                <img src="imagens/menu.png">
            </div>
        </nav>
        <!-- end nav -->
        <!-- Search Bar -->
        <main>
            <div class="text-bx">
                <h1><b>Escolha o seu livro e mergulhe no mar da sabedoria!</b></h1>
                <div class="input-bx">
                    <form action="pesquisa.php" method="POST">
                        <input type="text" name="pesquisa" placeholder="Pesquise seu livro">
                        <button type="submit" class="btn" name="submit" id="submit">Pesquisar</button>
                    </form>
                </div>
            </div>
            <div class="img-bx">
                <img src="imagens/Biblioteca2.png">
            </div>
        </main>
        <!-- End Search Bar -->
        <!-- Concertar isso -->
        <div class="Popular">
            <h2 class="popular">POPULAR</h2>
            <div class="popF">
            <img class="dom" src="imagens/domQuixote.jpeg">
            <img class="L1" src="imagens/1.jpg">
            <img class="L2" src="imagens/2.jpg">
            <img class="L3" src="imagens/3.jpg">
            <img class="sen" src="imagens/senhorAneis.jpeg">
            </div>
        </div>
        <div class="novos">
            <h2 class="novidades">NOVIDADES</h2>
            <div class="nov">
             <img class="L4" src="imagens/4.jpg">
             <img class="L5" src="imagens/5.jpg">
             <img class="L6" src="imagens/6.jpg">
             <img class="pen" src="imagens/PenseDeNovo.jpg">
             <img class="qua" src="imagens/quandoNinguem.jpg">
            </div>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>