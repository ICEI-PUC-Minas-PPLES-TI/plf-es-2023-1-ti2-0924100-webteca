<?php
if(isset($_POST['submit'])){
    include_once('config.php');
    $CPF = $_POST['CPF'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $pagamento = $_POST['pagamento'];
    if(strlen($senha) < 8){
        //Caso a senha tenha menos de 8 caracteres.
        echo "<script>alert('Senha precisa ter no mínimo 8 caracteres!');</script>";
        echo "<script>window.open('login.html', '_self');</script>";
        die();
    }
    //Verefica os dados na base para impedir informações repetidas;
    $query = "SELECT * FROM leitores WHERE CPF_Leitor = '$CPF' OR Nome_Leitor = '$nome' 
    OR Email_Leitor = '$email' OR Senha_Leitor = '$senha'";
    $result = mysqli_query($conexao, $query);
    $count = mysqli_num_rows($result);
    if($count > 0){
        echo "<script>alert('Uma das informações inseridas já existe no banco de dados!');</script>";
        echo "<script>window.open('login.html', '_self');</script>";
        die();
    }
    //Cartão de credito escolhido;
    if($pagamento == 'credito'){
        $redirectURL = "credito.html?CPF=" . urlencode($CPF) . "&nome=" . urlencode($nome) . 
        "&email=" . urlencode($email) . "&senha=" . urlencode($senha) . "&pagamento=" . urlencode($pagamento);
        header("Location: " . $redirectURL);
        die();
    }
    //Insere as informações no banco de dados;
    $insertQuery = "INSERT INTO leitores (CPF_Leitor, Nome_Leitor, Email_Leitor, Senha_Leitor, Pagamento) 
    VALUES ('$CPF', '$nome', '$email', '$senha', '$pagamento')";
    $insertResult = mysqli_query($conexao, $insertQuery);
    if($insertResult){
        header("Location: login.html");
        die();
    } 
    else{
        echo "<script>alert('Erro ao inserir os dados no banco!');<script>";
        echo "<script>window.open('login.html', '_self');</script>";
        die();
    }
}
?>