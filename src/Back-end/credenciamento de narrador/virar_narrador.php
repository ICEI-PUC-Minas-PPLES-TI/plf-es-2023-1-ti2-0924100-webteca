<?php
session_start();
if (isset($_POST['submit']) && isset($_SESSION['Nome_Leitor'])){
    //Verifica se o arquivo foi enviado corretamente;
    if(isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] === UPLOAD_ERR_OK){
        //Acessa o banco de dados;
        include_once('config.php');

        //Procura o ID_Leitor por meio do Nome_Leitor;
        $Nome_Leitor = $_SESSION['Nome_Leitor'];
        $query = "SELECT ID_Leitor FROM leitores WHERE Nome_Leitor = '$Nome_Leitor'";
        $result = mysqli_query($conexao, $query);
        $row = mysqli_fetch_assoc($result);
        $ID_Leitor = $row['ID_Leitor'];

        //Verifica se o usuário já é um narrador;
        $checkQuery = "SELECT Nome_Narrador FROM narradores WHERE Nome_Narrador = '$Nome_Leitor'";
        $checkResult = mysqli_query($conexao, $checkQuery);
        $alreadyExists = mysqli_num_rows($checkResult) > 0;
        if($alreadyExists){
            //Usuário já é um narrador;
            echo "<script>alert('Usuário já é um Narrador!');</script>";
            echo "<script>window.open('virar_narrador.html', '_self');</script>";
            exit;
        }

        //Pega a hora e a data atual;
        $Data_Envio = date('Y-m-d H:i:s');

        //Pega os dados do arquivo;
        $file_name = $_FILES['curriculo']['name'];
        $file_size = $_FILES['curriculo']['size'];
        $file_tmp = $_FILES['curriculo']['tmp_name'];
        $file_type = $_FILES['curriculo']['type'];

        //Verifica se o arquivo está no formato pdf;
        $allowed_types = ['application/pdf'];
        if(!in_array($file_type, $allowed_types)){
            //Formato inválido;
            echo "<script>alert('Erro! Apenas arquivos PDF são permitidos.');</script>";
            echo "<script>window.open('virar_narrador.html', '_self');</script>";
            exit;
        }

        //Lê o conteudo do arquivo;
        $content = file_get_contents($file_tmp);
        $content = mysqli_real_escape_string($conexao, $content);

        //Insere os dados na tabela;
        $insertQuery = "INSERT INTO curriculo (ID_Leitor, Arquivo, Data_Envio, Data_Resposta, Data_Dif, Resposta) 
        VALUES ('$ID_Leitor', '$content', '$Data_Envio', NULL, NULL, NULL)";
        $result = mysqli_query($conexao, $insertQuery);
        if($result){
            //Sucesso;
            echo "<script>alert('Currículo enviado com Sucesso!');</script>";
            echo "<script>window.open('virar_narrador.html', '_self');</script>";
            exit;
        } 
        else{
            //Erro na inserção no banco de dados;
            echo "<script>alert('Erro! Incapaz de enviar Currículo!');</script>";
            echo "<script>window.open('virar_narrador.html', '_self');</script>";
            exit;
        }
    } 
    else{
        //Erro ao fazer upload do arquivo;
        echo "<script>alert('Erro! Falha no upload do arquivo.');</script>";
        echo "<script>window.open('virar_narrador.html', '_self');</script>";
        exit;
    }
} 
else{
    //Currículo ausente;
    echo "<script>alert('Erro! Currículo ausente ou usuário não logado.');</script>";
    echo "<script>window.open('virar_narrador.html', '_self');</script>";
}
?>