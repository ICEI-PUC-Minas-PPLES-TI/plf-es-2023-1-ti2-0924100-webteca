<?php
session_start();
if(isset($_POST['submit']) && !empty($_POST['livro']) 
&& !empty($_POST['motivo']) && isset($_SESSION['Nome_Leitor'])){
    //Acessa o banco de dados;
    include_once('config.php');
    $nome_livro = $_POST['livro'];//Nome do livro que o usuário quer remover;
    $nome_leitor = $_SESSION['Nome_Leitor'];//Pega o nome do usuário que está mandando o pedido;
    $sql = "SELECT * FROM livros WHERE Nome_Livro = '$nome_livro'";//Procura o livro no banco de dados;
    $result = $conexao->query($sql);
    if(mysqli_num_rows($result) < 1){
        //Caso o Livro não exista no banco de dados;
        echo "<script>alert('Erro! Livro não encontrado!');</script>";
        echo "<script>window.open('pedir_remocaolivro.html', '_self');</script>";
        exit();
    }
    else{
        //Caso exista o Livro;
        $row = mysqli_fetch_assoc($result);
        $ID_Narrador = $row['ID_Narrador'];//Pega o ID do narrador do livro;
        //Procura na tabela "leitores" por meio do nome do leitor;
        $leitoresSql = "SELECT * FROM leitores WHERE Nome_Leitor = '$nome_leitor'";
        $leitoresResult = $conexao->query($leitoresSql);
        if(mysqli_num_rows($leitoresResult) < 1){
            //Leitor inexistente;
            echo "<script>alert('Erro! Leitor não encontrado!');</script>";
            echo "<script>window.open('pedir_remocaolivro.html', '_self');</script>";
            exit();
        }
        else{
            //Leitor achado;
            $leitoresRow = mysqli_fetch_assoc($leitoresResult);
            $ID_Leitor = $leitoresRow['ID_Leitor'];//ID do leitor que mandou a reclamação;
            $motivo = $_POST['motivo'];//Armazena o motivo por trás do pedido de remoção;
            $Data_Envio = date('Y-m-d H:i:s');//Pega a hora e a data atual;

            $result = mysqli_query($conexao, "INSERT INTO pedido_remocao (Nome_Livro, 
            ID_Narrador, ID_Leitor, Motivo, Data_Envio, Data_Resposta, Data_Dif, Resposta) 
            VALUES ('$nome_livro', '$ID_Narrador', '$ID_Leitor', '$motivo', '$Data_Envio', NULL, NULL, NULL)");
            echo "<script>alert('Pedido enviado com sucesso!');</script>";
            echo "<script>window.open('pedir_remocaolivro.html', '_self');</script>";
            exit();
        }
    }
}
else{
    //Não acessa o banco de dados;
    echo "<script>alert('Erro! Banco de dados não pode ser Acessado!');</script>";
    echo "<script>window.open('pedir_remocaolivro.html', '_self');</script>";
    exit();
}
?>