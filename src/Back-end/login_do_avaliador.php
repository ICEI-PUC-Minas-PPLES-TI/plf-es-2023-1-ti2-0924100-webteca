<?php
session_start();
if(isset($_POST['submit']) && !empty($_POST['nome_avaliador']) 
&& !empty($_POST['senha_avaliador'])){
    //Acessa;
    include_once('config.php');
    $nome_avaliador = $_POST['nome_avaliador'];
    $senha_avaliador = $_POST['senha_avaliador'];
    $sql = "SELECT * FROM avaliadores WHERE Nome_Avaliador = '$nome_avaliador' and 
    Senha_Avaliador = '$senha_avaliador'";//Procura no banco pelo avaliador;
    $result = $conexao->query($sql);
    if(mysqli_num_rows($result) < 1){
        //Caso não exista o avaliador;
        unset($_SESSION['Nome_Avaliador']);
        unset($_SESSION['Senha_Avaliador']);
        header("Location: login_do_avaliador.html");
    }
    else{
        //Caso exista o avaliador;
        $_SESSION['Nome_Avaliador'] = $nome_avaliador;
        $_SESSION['Senha_Avaliador'] = $senha_avaliador;
        header("Location: index_Avaliador.php");
    }
}
else{
    //Não acessa;
    header("Location: login_do_avaliador.html");
}
?>