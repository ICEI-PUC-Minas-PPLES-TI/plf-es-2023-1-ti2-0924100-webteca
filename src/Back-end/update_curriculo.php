<?php
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['Nome_Avaliador'])){
    include_once('config.php');//Cria a conexão com o banco de dados;
    $curriculoID = $_POST["curriculo_id"];
    $action = $_POST["action"];
    $dataResposta = date("Y-m-d H:i:s");//Pega a data e tempo atual;
    $query = "SELECT Data_Envio FROM curriculo WHERE ID_Curriculo = '$curriculoID'";
    $result = mysqli_query($conexao, $query);
    /*************************************************************************/
    if($result && $result->num_rows > 0){
        $row = mysqli_fetch_assoc($result);
        $dataEnvio = $row["Data_Envio"];
        $datetime1 = new DateTime($dataEnvio);
        $datetime2 = new DateTime($dataResposta);
        $interval = $datetime1->diff($datetime2);
        $dataDif = $interval->format('%H:%i:%s');
        //Usuário foi aprovado. Ele vira um narrador;
        if($action == "aprovar"){
            //Verifica se o usuário já foi avaliado;
            $queryCheck = "SELECT Resposta FROM curriculo WHERE ID_Curriculo = $curriculoID";
            $resultCheck = mysqli_query($conexao, $queryCheck);
            if($resultCheck && $resultCheck->num_rows > 0){
                $rowCheck = mysqli_fetch_assoc($resultCheck);
                $respostaCheck = $rowCheck["Resposta"];
                if(!is_null($respostaCheck)){
                    echo "<script>alert('Erro: Pedido já foi avaliado.');</script>";
                    echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                    exit();
                }
                else{$resposta = "Aprovado";}
            }
            else{
                echo "<script>alert('Erro: Não foi possível verificar o status do pedido.');</script>";
                echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                exit();
            }
            //Pega informações de leitores por meio do ID_Leitor;
            $query = "SELECT ID_Leitor, CPF_Leitor, Nome_Leitor, Email_Leitor, Senha_Leitor FROM leitores WHERE 
            ID_Leitor = (SELECT ID_Leitor FROM curriculo WHERE ID_Curriculo = $curriculoID)";
            $result = mysqli_query($conexao, $query);
            if($result && $result->num_rows > 0){
                $rowLeitor = mysqli_fetch_assoc($result);
                $id_leitor = $rowLeitor["ID_Leitor"];//ID_leitor;
                $cpfNarrador = $rowLeitor["CPF_Leitor"];
                $nomeNarrador = $rowLeitor["Nome_Leitor"];
                $emailNarrador = $rowLeitor["Email_Leitor"];
                $senhaNarrador = $rowLeitor["Senha_Leitor"];
                //Verifica se o narrador já existe na tabela (para prevenir repetidos);
                $queryCheckNarrador = "SELECT * FROM narradores 
                WHERE CPF_Narrador = '$cpfNarrador'";
                $resultCheckNarrador = mysqli_query($conexao, $queryCheckNarrador);
                if($resultCheckNarrador && $resultCheckNarrador->num_rows > 0){
                    $resposta = "Rejeitado";
                    //Fazer update da tabela curriculo;
                    $query = "UPDATE curriculo SET Data_Resposta = '$dataResposta', Data_Dif = '$dataDif', 
                    Resposta = '$resposta' WHERE ID_Curriculo = $curriculoID";
                    $result = mysqli_query($conexao, $query);
                    if($result){
                        echo "<script>alert('Erro: O narrador já existe no banco de dados!');</script>";
                        echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                        exit();
                    } 
                    else{
                        echo "Erro: " . mysqli_error($conexao);
                        echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                        exit();
                    }
                }
                //Inserir valores na tabela de narradores;
                $queryInsert = "INSERT INTO narradores (CPF_Narrador, Nome_Narrador, Email_Narrador, Senha_Narrador, ID_Curriculo) 
                VALUES ('$cpfNarrador', '$nomeNarrador', '$emailNarrador', '$senhaNarrador', $curriculoID)";
                $resultInsert = mysqli_query($conexao, $queryInsert);
                if(!$resultInsert){
                    echo "Erro ao inserir na tabela narradores: " . mysqli_error($conexao);
                    echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                    exit();
                }
                //Manda notificação para o usuário/remetende do pedido;
                $descricao = "Seu currículo foi aprovado.\nAgora que você é um Narrador, você pode aceitar pedidos e postar livros.";
                $queryNoti = "INSERT INTO notificacao (Descricao,ID_Leitor) VALUES ('$descricao', '$id_leitor')";
                $resultNoti = mysqli_query($conexao, $queryNoti);
                if(!$resultNoti){
                    echo "Erro ao mandar a notificação: " . mysqli_error($conexao);
                    echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                    exit();
                }
            }
            else{
                echo "<script>alert('Erro: Não foi possível recuperar informações da tabela leitores.');</script>";
                echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                exit();
            }
        }
        //Usuário rejeitado. Ele não será um narrador;
        elseif($action == "rejeitar"){
            //Verifica se o usuário já foi avaliado;
            $queryCheck = "SELECT Resposta FROM curriculo WHERE ID_Curriculo = $curriculoID";
            $resultCheck = mysqli_query($conexao, $queryCheck);
            if($resultCheck && $resultCheck->num_rows > 0){
                $rowCheck = mysqli_fetch_assoc($resultCheck);
                $respostaCheck = $rowCheck["Resposta"];
                if(!is_null($respostaCheck)){
                    echo "<script>alert('Erro: Pedido já foi avaliado.');</script>";
                    echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                    exit();
                }
                else{$resposta = "Rejeitado";}
            }
            else{
                echo "<script>alert('Erro: Não foi possível verificar o status do pedido.');</script>";
                echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                exit();
            }
            //Manda notificação para o usuário/remetende do pedido;
            $queryleitor = "SELECT ID_Leitor FROM leitores WHERE 
            ID_Leitor = (SELECT ID_Leitor FROM curriculo WHERE ID_Curriculo = $curriculoID)";
            $resultleitor = mysqli_query($conexao, $queryleitor);
            if($resultleitor && $resultleitor->num_rows > 0){
                $rowLeitor = mysqli_fetch_assoc($resultleitor);
                $id_leitor = $rowLeitor["ID_Leitor"];//ID_leitor;
                $descricao = "Seu currículo foi rejeitado.\nVocê não possui as credenciais necessárias.";
                $queryNoti = "INSERT INTO notificacao (Descricao,ID_Leitor) VALUES ('$descricao', '$id_leitor')";
                $resultNoti = mysqli_query($conexao, $queryNoti);
                if(!$resultNoti){
                    echo "Erro ao mandar a notificação: " . mysqli_error($conexao);
                    echo "<script>window.open('colocar_narrador.php', '_self');</script>";
                    exit();
                }
            }
        }
        /*************************************************************************/
        //Fazer update da tabela curriculo;
        $query = "UPDATE curriculo SET Data_Resposta = '$dataResposta', Data_Dif = '$dataDif', 
        Resposta = '$resposta' WHERE ID_Curriculo = $curriculoID";
        $result = mysqli_query($conexao, $query);
        if($result){
            echo "<script>alert('Processo terminado!');</script>";
            echo "<script>window.open('colocar_narrador.php', '_self');</script>";
            exit();
        } 
        else{
            echo "Erro: " . mysqli_error($conexao);
            echo "<script>window.open('colocar_narrador.php', '_self');</script>";
            exit();
        }
    }
    else{
        echo "<script>alert('Erro: Tabela currículo está vazia.');</script>";
        echo "<script>window.open('colocar_narrador.php', '_self');</script>";
        exit();
    }
}
else{
    echo "<script>alert('Erro! Avaliador não logado ou outro problema.');</script>";
    echo "<script>window.open('colocar_narrador.php', '_self');</script>";
    exit();
}
?>