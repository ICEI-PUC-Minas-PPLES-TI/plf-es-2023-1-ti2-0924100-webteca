<?php
session_start();
include_once('config.php');
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['Nome_Avaliador'])){
    $pedidoRemocaoId = $_POST['pedidoremocao_id'];
    $action = $_POST['action'];
    //Pega as informações sobre o livro da tabela pedido_remocao;
    $query = "SELECT Nome_Livro, ID_Narrador, 
    ID_Leitor, Motivo, Data_Envio FROM pedido_remocao WHERE ID_PedidoRemocao = $pedidoRemocaoId";
    $result = mysqli_query($conexao, $query);
    $row = mysqli_fetch_assoc($result);
    $nome_livro = $row['Nome_Livro'];
    $id_narrador = $row['ID_Narrador'];
    $id_leitor = $row['ID_Leitor'];
    $motivo = $row['Motivo'];
    $dataEnvio = $row["Data_Envio"];//Data de envio do pedido;
    /*************************************************************************/
    //O pedido foi aceito;
    if($action == "aceitar"){
        //Verifica se o usuário já foi avaliado;
        $queryCheck = "SELECT Resposta FROM pedido_remocao WHERE ID_PedidoRemocao = $pedidoRemocaoId";
        $resultCheck = mysqli_query($conexao, $queryCheck);
        if($resultCheck && $resultCheck->num_rows > 0){
            $rowCheck = mysqli_fetch_assoc($resultCheck);
            $respostaCheck = $rowCheck["Resposta"];
            if(!is_null($respostaCheck)){
                echo "<script>alert('Erro: Pedido já foi avaliado.');</script>";
                echo "<script>window.open('remover_livro.php', '_self');</script>";
                exit();
            }
            else{$resposta = "Aceito";}
        }
        else{
            echo "<script>alert('Erro: Não foi possível verificar o status do pedido.');</script>";
            echo "<script>window.open('remover_livro.php', '_self');</script>";
            exit();
        }
        //Deleta o livro da base de dados;
        $deleteQuery = "DELETE FROM livros WHERE Nome_Livro = ?";
        $stmt = mysqli_prepare($conexao, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "s", $nome_livro);
        mysqli_stmt_execute($stmt);
        if(mysqli_affected_rows($conexao) > 0){
            $safeLivroName = preg_replace('/[^A-Za-z0-9\-]/', '', $nome_livro);
            $htmlFilePath = "livros/" . $safeLivroName . ".html";

            //Deleta o arquivo html correspondente;
            if(file_exists($htmlFilePath)){unlink($htmlFilePath);}

            //Manda notificação para o narrador do livro;
            $queryGetLeitorId = "SELECT leitores.ID_Leitor FROM narradores 
            INNER JOIN leitores ON narradores.Nome_Narrador = leitores.Nome_Leitor
            WHERE narradores.ID_Narrador = '$id_narrador'";
            $resultGetLeitorId = mysqli_query($conexao, $queryGetLeitorId);
            if($resultGetLeitorId && $resultGetLeitorId->num_rows > 0){
                $descricao = "Lamentamos informá-lo, mas seu livro $nome_livro foi removido por $motivo.";
                $rowLeitorId = mysqli_fetch_assoc($resultGetLeitorId);
                $true_idnarrador = $rowLeitorId['ID_Leitor'];
                $queryNoti = "INSERT INTO notificacao (Descricao, ID_Leitor) VALUES ('$descricao', '$true_idnarrador')";
                $resultNoti = mysqli_query($conexao, $queryNoti);
                if(!$resultNoti){
                    echo "Erro ao enviar a notificação: " . mysqli_error($conexao);
                    echo "<script>window.open('remover_livro.php', '_self');</script>";
                    exit();
                }
            }
            //Manda notificação para o remetente da reclamação;
            $descricao = "Seu pedido de remoção para o livro $nome_livro foi aceito.";
            $queryNoti = "INSERT INTO notificacao (Descricao,ID_Leitor) VALUES ('$descricao', '$id_leitor')";
            $resultNoti = mysqli_query($conexao, $queryNoti);
            if(!$resultNoti){
                echo "Erro ao mandar a notificação: " . mysqli_error($conexao);
                echo "<script>window.open('remover_livro.php', '_self');</script>";
                exit();
            }
        } 
        else{
            echo "<script>alert('O livro não pode ser deletado!');</script>";
            echo "<script>window.open('remover_livro.php', '_self');</script>";
            exit;
        }
    }
    //O pedido/reclamação foi recusado;
    elseif($action == "recusar"){
        $queryCheck = "SELECT Resposta FROM pedido_remocao WHERE ID_PedidoRemocao = $pedidoRemocaoId";
        $resultCheck = mysqli_query($conexao, $queryCheck);
        if($resultCheck && $resultCheck->num_rows > 0){
            $rowCheck = mysqli_fetch_assoc($resultCheck);
            $respostaCheck = $rowCheck["Resposta"];
            if(!is_null($respostaCheck)){
                echo "<script>alert('Erro: Pedido já foi avaliado.');</script>";
                echo "<script>window.open('remover_livro.php', '_self');</script>";
                exit();
            }
            else{$resposta = "Recusar";}
        }
        else{
            echo "<script>alert('Erro: Não foi possível verificar o status do pedido.');</script>";
            echo "<script>window.open('remover_livro.php', '_self');</script>";
            exit();
        }
        //Manda notificação para o remetente da reclamação;
        $descricao = "Seu pedido de remoção para o livro $nome_livro foi recusado.";
        $queryNoti = "INSERT INTO notificacao (Descricao,ID_Leitor) VALUES ('$descricao', '$id_leitor')";
        $resultNoti = mysqli_query($conexao, $queryNoti);
        if(!$resultNoti){
            echo "Erro ao mandar a notificação: " . mysqli_error($conexao);
            echo "<script>window.open('remover_livro.php', '_self');</script>";
            exit();
        }
    }
    /*************************************************************************/
    //Fazer update da tabela Pedido de remoção;
    $datetime1 = new DateTime($dataEnvio);
    $datetime2 = new DateTime($dataResposta);
    $interval = $datetime1->diff($datetime2);
    $dataDif = $interval->format('%H:%i:%s');
    $dataResposta = date("Y-m-d H:i:s");//Pega a data e tempo atual;
    $query = "UPDATE pedido_remocao SET Data_Resposta = '$dataResposta', Data_Dif = '$dataDif', Resposta = '$resposta' 
    WHERE ID_PedidoRemocao = $pedidoRemocaoId";
    $result = mysqli_query($conexao, $query);
    if($result){
        echo "<script>alert('Pedido avaliado com sucesso!');</script>";
        echo "<script>window.open('remover_livro.php', '_self');</script>";
        exit();
    } 
    else{
        echo "Erro: " . mysqli_error($conexao);
        echo "<script>window.open('remover_livro.php', '_self');</script>";
        exit();
    }
} 
else{
    echo "<script>alert('Avaliador não logado!');</script>";
    echo "<script>window.open('remover_livro.php', '_self');</script>";
    exit;
}
?>