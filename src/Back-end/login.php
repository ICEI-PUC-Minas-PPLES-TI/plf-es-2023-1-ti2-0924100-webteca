<?php
session_start();
if(isset($_POST['submit']) && !empty($_POST['nome_usuario']) 
&& !empty($_POST['senha_usuario'])){
    //Acessa o banco de dados;
    include_once('config.php');
    $nome_usuario = $_POST['nome_usuario'];
    $senha_usuario = $_POST['senha_usuario'];
    $sql = "SELECT * FROM leitores WHERE Nome_Leitor = '$nome_usuario' and Senha_Leitor = '$senha_usuario'";
    $result = $conexao->query($sql);
    if(mysqli_num_rows($result) < 1){
        //Caso não exista o usuário;
        unset($_SESSION['Nome_Leitor']);
        unset($_SESSION['Senha_Leitor']);
        echo "<script>alert('Senha ou nome de usuário incorreto!');</script>";
        echo "<script>window.open('login.html', '_self');</script>";
    }
    else{
        //Caso exista o usuário;
        $_SESSION['Nome_Leitor'] = $nome_usuario;
        $_SESSION['Senha_Leitor'] = $senha_usuario;
        header("Location: index_Leitor.php");
    }
}
else{
    //Não acessa o banco de dados;
    header("Location: login.html");
}
?>