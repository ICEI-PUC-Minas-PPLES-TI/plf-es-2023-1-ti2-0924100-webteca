<?php
function createBookHTML($nomeLivro, $nomeEscritor, $tema, $edicao, 
$idioma, $descricao, $ID_Narrador, $ID_PedidoLivro, $conexao){
    //Pega a capa e o audio;
    $queryLivro = "SELECT audio, capa, descricao FROM livros WHERE Nome_Livro = ?";
    $stmtLivro = mysqli_prepare($conexao, $queryLivro);
    mysqli_stmt_bind_param($stmtLivro, "s", $nomeLivro);
    mysqli_stmt_execute($stmtLivro);
    $resultLivro = mysqli_stmt_get_result($stmtLivro);
    $rowLivro = mysqli_fetch_assoc($resultLivro);
    $audio = $rowLivro['audio'];
    $capa = $rowLivro['capa'];
    //Cria o html;
    $htmlOutput = "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$nomeLivro</title>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
        <!-- Additional CSS styles -->
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Roboto',sans-serif;
            }
            body{
                background-color: white;
                min-height: 100vh;
            }
            .container{
                max-width: 1300px;
                margin: 0 auto;
                padding: 0 4%;
            }
            .fotoLivro{
                display: flex;
            }
            .capa{
                width: 40%;
                margin-right: 50px;
            }
            .escrita{
                display: block;
                margin-top: 20px;
            }
            .audio-container{
                margin-top: 500px;
                position: relative;
                left: -350px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <section id='detalhes' class='detalhesLivro'>
                <div class='livro'>
                <div class='fotoLivro'>
                    <img class='capa' src='data:image/jpeg;base64," . base64_encode($capa) . "' id='MainImg' alt=''>
                    <div class='escrita'>
                        <div class='titulo'>
                            <h1>$nomeLivro</h1>
                        </div>
                        <div class='escritor'>
                            <p><strong>Escritor: </strong>$nomeEscritor<br></p>
                        </div>
                        <div class='categoria'>
                            <p><strong>Categoria: </strong>$tema</p>
                        </div>
                        <div class='categoria'>
                            <p><strong>Edição: </strong>$edicao</p>
                        </div>
                        <div class='categoria'>
                            <p><strong>Idioma: </strong>$idioma</p>
                        </div>
                        <div class='descricao'>
                            <p><strong>Descrição: </strong>$descricao</p>
                        </div>
                    </div>
                    <div class='audio-container'>
                        <audio controls>
                        <source src='data:audio/mp3;base64," . base64_encode($audio) . "' type='audio/mp3'>
                        Your browser does not support the audio element.
                        </audio>
                    </div>
                </div>
            </section>
        </div>
    </body>
    </html>";
    //Cria um identificador unico para o arquivo html;
    $htmlIdentifier = $nomeLivro;//Usa o título do livro como identificador;
    $file = 'livros/' . $htmlIdentifier . '.html';//Cria um caminho para o html;
    //Verifica se o arquivo já existe;
    if(file_exists($file)){
        echo "Erro! Esse livro já existe no banco de dados.";
        exit();
    } 
    else{
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
            $resposta = "Atendido";
            $queryPedido = "UPDATE pedido_livro SET Data_Resposta = '$dataResposta', Data_Dif = '$dataDif', Resposta = '$resposta' 
            WHERE ID_PedidoLivro = $ID_PedidoLivro";
            $resultPedido = mysqli_query($conexao, $queryPedido);
            if(file_put_contents($file, $htmlOutput) !== false && $resultPedido){
                echo "Livro Adicionado com sucesso!";
                exit();
            }
            else{
                echo "Erro: HTML não pode ser salva como $file!";
                exit();
            }
        }
        else{
            echo "Erro: Pedido de livro não achado.";
            exit();
        }
    }
}
?>