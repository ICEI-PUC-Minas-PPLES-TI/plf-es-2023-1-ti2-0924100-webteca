<?php
session_start();
if(isset($_SESSION['Nome_Leitor'])){
    include_once('config.php');
    $ID_PedidoLivro = $_POST["ID_PedidoLivro"];
    $nomeLivro = $_POST["nomeLivro"];
    $nomeEscritor = $_POST["nomeEscritor"];
    $tema = $_POST["tema"];
    $edicao = $_POST["edicao"];
    $idioma = $_POST["idioma"];
    $audio = $_FILES["audio"]["tmp_name"];
    $capa = $_FILES["capa"]["tmp_name"];
    $descricao = $_POST["descricao"];
    //Verifica se o livro já existe;
    $queryCheckLivro = "SELECT COUNT(*) AS numLivros FROM livros WHERE Nome_Livro = ?";
    $stmtCheckLivro = mysqli_prepare($conexao, $queryCheckLivro);
    mysqli_stmt_bind_param($stmtCheckLivro, "s", $nomeLivro);
    mysqli_stmt_execute($stmtCheckLivro);
    $resultCheckLivro = mysqli_stmt_get_result($stmtCheckLivro);
    $rowCheckLivro = mysqli_fetch_assoc($resultCheckLivro);
    $numLivros = $rowCheckLivro['numLivros'];
    if($numLivros > 0){
        echo "Livro já foi postado (ou um livro com um nome idêntico já existe).";
        //Fazer update da tabela Pedidos de livros;
        $dataResposta = date("Y-m-d H:i:s");//Pega a data e tempo atual;
        $query = "SELECT Data_Envio FROM pedido_livro WHERE ID_PedidoLivro = '$ID_PedidoLivro'";
        $result = mysqli_query($conexao, $query);
        if($result && $result->num_rows > 0){
            $row = mysqli_fetch_assoc($result);
            $dataEnvio = $row["Data_Envio"];
            $datetime1 = new DateTime($dataEnvio);
            $datetime2 = new DateTime($dataResposta);
            $interval = $datetime1->diff($datetime2);
            $dataDif = $interval->format('%H:%i:%s');
            $resposta = "Inválido";
            $queryPedido = "UPDATE pedido_livro SET Data_Resposta = '$dataResposta', Data_Dif = '$dataDif', Resposta = '$resposta' 
            WHERE ID_PedidoLivro = $ID_PedidoLivro";
            $resultPedido = mysqli_query($conexao, $queryPedido);
        }
        exit();
    }
    //Verifica se o audio está no formato MP3;
    $audioFileType = $_FILES["audio"]["type"];
    if($audioFileType !== "audio/mpeg"){
        echo "O arquivo de áudio deve estar no formato MP3.";
        exit();
    }
    //Verifica se a capa está no formato de uma imagem;
    $capaFileType = $_FILES["capa"]["type"];
    if(!in_array($capaFileType, ["image/jpeg"])){
        echo "A capa do livro deve ser uma imagem (JPEG).";
        exit();
    }
    //Pega ID_Leitor por meio do ID_PedidoLivro;
    $queryPedidoLivro = "SELECT ID_Leitor FROM pedido_livro WHERE ID_PedidoLivro = '$ID_PedidoLivro'";
    $resultPedidoLivro = mysqli_query($conexao, $queryPedidoLivro);
    if($resultPedidoLivro && $resultPedidoLivro->num_rows > 0){
        $rowPedidoLivro = mysqli_fetch_assoc($resultPedidoLivro);
        $id_leitor = $rowPedidoLivro["ID_Leitor"];
    }
    else{
        echo "Erro ao obter o ID do Leitor.";
        exit();
    }
    //Pega ID_Narrador com base no Nome_Leitor;
    $Nome_Leitor = $_SESSION['Nome_Leitor'];
    $queryNarrador = "SELECT ID_Narrador FROM narradores WHERE Nome_Narrador = '$Nome_Leitor'";
    $resultNarrador = mysqli_query($conexao, $queryNarrador);
    //Verifica se o usuário, pessoa que está postando o livro, é um narrador;
    if($resultNarrador && $resultNarrador->num_rows > 0){
        //É um narrador;
        $rowNarrador = mysqli_fetch_assoc($resultNarrador);
        $ID_Narrador = $rowNarrador["ID_Narrador"];
        //Insere os dados na tabela livros;
        $audioContent = file_get_contents($audio);
        $capaContent = file_get_contents($capa);
        $queryInsertLivro = "INSERT INTO livros (Nome_Livro, Nome_Escritor, Tema, Edicao, Idioma, Audio, Capa, Descricao, ID_Narrador) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $queryInsertLivro);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $nomeLivro, $nomeEscritor, $tema, $edicao, $idioma, 
        $audioContent, $capaContent, $descricao, $ID_Narrador);
        //Manda notificação para o usuário/remetende do pedido;
        $descricao_noti = "Seus pedido para o livro $nomeLivro foi atendido. Ele agora deve estar disponível!";
        $queryNoti = "INSERT INTO notificacao (Descricao,ID_Leitor) VALUES ('$descricao_noti', '$id_leitor')";
        $resultNoti = mysqli_query($conexao, $queryNoti);
        //Verifica se os processos foram feitos com sucesso;
        if(!mysqli_stmt_execute($stmt) OR !$resultNoti){
            echo "Erro ao adicionar o livro ou ao mandar a notificação.";
            exit();
        }
        //Cria um html para o livro;
        $conexao = mysqli_init();
        mysqli_ssl_set($conexao,NULL,NULL, "DigiCertGlobalRootCA.crt.pem", NULL, NULL);
        mysqli_real_connect($conexao, "banco-webteca.mysql.database.azure.com", "Gabriel", 
        "Oliver#pato15", "WebtecaTables", 3306, MYSQLI_CLIENT_SSL);
        if(mysqli_connect_errno()){
            echo "Falha ao connectar com a base de dados: " . mysqli_connect_error();
            exit();
        }   
        include 'livros/create_livrohtml.php';
        createBookHTML($nomeLivro, $nomeEscritor, $tema, $edicao, 
        $idioma, $descricao, $ID_Narrador, $ID_PedidoLivro, $conexao);
    } 
    else{
        //Caso a pessoa que esteja enviando, não seja um narrador;
        echo "Aviso! Você não é um Narrador. Por favor mande seu currículo.";
        exit();
    }
} 
else{
    echo "Erro! Usuário não logado.";
    exit();
}
?>