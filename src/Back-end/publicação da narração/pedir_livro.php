<?php
session_start();
if(isset($_POST['submit']) && !empty($_POST['livro']) && isset($_SESSION['Nome_Leitor'])){
    //Acessa o banco de dados;
    include_once('config.php');
    //Procura o ID_Leitor por meio do Nome_Leitor;
    $Nome_Leitor = $_SESSION['Nome_Leitor'];
    $query = "SELECT ID_Leitor, Pagamento FROM leitores WHERE Nome_Leitor = '$Nome_Leitor'";
    $result = mysqli_query($conexao, $query);
    $row = mysqli_fetch_assoc($result);
    $ID_Leitor = $row['ID_Leitor'];//ID do leitor;
    $pagamento = $row['Pagamento'];//Forma de Pagamento;
    $nome_livro = $_POST['livro'];//Nome do livro que o usuário pediu;
    $Data_Envio = date('Y-m-d H:i:s');//Pega a hora e a data atual;
    //Verifica se o leitor é Premium;
    if($pagamento != "credito"){
        echo "<script>alert('Você precisa ser um usuário Premium para pedir um livro.');</script>";
        echo "<script>window.open('pedir_livro.html', '_self');</script>";
        exit();
    }
    //Verifica se o livro já existe;
    $queryCheckLivro = "SELECT COUNT(*) AS numLivros FROM livros WHERE Nome_Livro = ?";
    $stmtCheckLivro = mysqli_prepare($conexao, $queryCheckLivro);
    mysqli_stmt_bind_param($stmtCheckLivro, "s", $nome_livro);
    mysqli_stmt_execute($stmtCheckLivro);
    $resultCheckLivro = mysqli_stmt_get_result($stmtCheckLivro);
    $rowCheckLivro = mysqli_fetch_assoc($resultCheckLivro);
    $numLivros = $rowCheckLivro['numLivros'];
    if($numLivros > 0){
        echo "<script>alert('Livro já foi postado (ou um livro com um nome idêntico já existe).');</script>";
        echo "<script>window.open('pedir_livro.html', '_self');</script>";
        exit();
    }
    //Insere o pedido na tabela;
    $insertQuery = "INSERT INTO pedido_livro (ID_Leitor, Nome_Livro, Data_Envio, Data_Resposta, Data_Dif, Resposta) 
    VALUES ('$ID_Leitor', '$nome_livro', '$Data_Envio', NULL, NULL, NULL)";
    $result = mysqli_query($conexao, $insertQuery);
    if($result){
        //Sucesso;
        echo "<script>alert('Pedido enviado com Sucesso!');</script>";
        echo "<script>window.open('pedir_livro.html', '_self');</script>";
        exit;
    } 
    else{
        //Erro na inserção no banco de dados;
        echo "<script>alert('Erro! Incapaz de enviar pedido!');</script>";
        echo "<script>window.open('pedir_livro.html', '_self');</script>";
        exit;
    }
}
else{
    //Não acessa o banco de dados;
    echo "<script>alert('Erro! Banco de dados não pode ser Acessado!');</script>";
    echo "<script>window.open('pedir_livro.html', '_self');</script>";
}
?>