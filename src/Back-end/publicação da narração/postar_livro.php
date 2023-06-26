<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Postar Livro</title>
    <style>
    table {
        border-collapse: collapse;
        width: 100%;
        color: #588c7e;
        font-family: monospace;
        font-size: 20px;
        text-align: left;
    }
    th {
        background-color: #588c7e;
        color: white;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
    .btn{
        border: none;
        background-color: #10cfcf;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        letter-spacing: 1px;
        margin-left: 20px;
        cursor: pointer;
    }
    .btn:hover {background:  #ef8092;}
    </style>
</head>
<body>
    <table>
        <tr>
            <th>ID-Pedido</th>
            <th>Nome do Leitor</th>
            <th>Nome Livro</th>
            <th>Data de Envio</th>
            <th>Data de Resposta</th>
            <th>Tempo Gasto</th>
            <th>Resposta</th>
            <th>Postar<th>
        </tr>
        <?php
        include_once('config.php');
        //Acessa o banco de dados e pega os pedidos de livro existentes;
        $query = "SELECT ID_PedidoLivro, ID_Leitor, Nome_Livro, Data_Envio, Data_Resposta, Data_Dif, Resposta 
        FROM pedido_livro";
        $result = mysqli_query($conexao, $query);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>" . $row["ID_PedidoLivro"] . "</td>";
                //Usa ID_Leitor para pegar o Nome_Leitor;
                $leitorQuery = "SELECT Nome_Leitor FROM leitores WHERE ID_Leitor = " . $row["ID_Leitor"];
                $leitorResult = mysqli_query($conexao, $leitorQuery);
                $leitorRow = mysqli_fetch_assoc($leitorResult);
                echo "<td>" . $leitorRow["Nome_Leitor"] . "</td>";
                echo "<td>" . $row["Nome_Livro"] . "</td>";
                echo "<td>" . $row["Data_Envio"] . "</td>";
                echo "<td>" . $row["Data_Resposta"] . "</td>";
                echo "<td>" . $row["Data_Dif"] . "</td>";
                echo "<td>" . $row["Resposta"] . "</td>";
                echo "<td><button onclick=\"openForm('" . $row["ID_PedidoLivro"] . "', '" . $row["Nome_Livro"] . "')\">Postar livro</button></td>";
                echo "</tr>";
            }
        } 
        else{
            echo "<tr><td colspan='7'>0 pedidos</td></tr>";
        }
        ?>
    </table>
    <?php
    include_once('config.php');
    $query = "SELECT Data_Dif FROM pedido_livro WHERE Data_Dif IS NOT NULL";
    $result = mysqli_query($conexao, $query);
    $totalRows = mysqli_num_rows($result);//Total de instâncias em que Data_Dif não é NULL;
    $totalDuration = 0;
    while($row = mysqli_fetch_assoc($result)){
        $durationParts = explode(':', $row['Data_Dif']);
        $duration = $durationParts[0] * 3600 + $durationParts[1] * 60 + $durationParts[2];
        $totalDuration += $duration;//Soma todos os Data_Dif em segundos;
    }
    if($totalRows > 0){
        $averageDuration = $totalDuration / $totalRows;//Calcula a média em segundos;
        //Tranforma em horas/minutos/segundos;
        $averageHours = floor($averageDuration / 3600);
        $averageMinutes = floor(($averageDuration % 3600) / 60);
        $averageSeconds = $averageDuration % 60;
        $averageDataDif = sprintf("%02d:%02d:%02d", $averageHours, $averageMinutes, $averageSeconds);
        //Mostra o resultado no html;
        echo "<p>Média do Tempo de Resposta: $averageDataDif</p>";
    }
    else{
        echo "<p>Nenhum Pedido de livro foi concluido ainda.</p>";
    }
    ?>
    <script>
        function openForm(ID_PedidoLivro, nomeLivro){
            var formHTML = `
            <form action="insert_livros.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="ID_PedidoLivro" value="${ID_PedidoLivro}">
                <input type="hidden" name="nomeLivro" value="${nomeLivro}">
                <label for="nomeEscritor">Nome Escritor:</label>
                <input type="text" name="nomeEscritor" required><br>
                <label for="tema">Tema:</label>
                <input type="text" name="tema" required><br>
                <label for="edicao">Edicao:</label>
                <input type="text" name="edicao" required><br>
                <label for="idioma">Idioma:</label>
                <input type="text" name="idioma" required><br>
                <label for="audio">Audio:</label>
                <input type="file" name="audio" required><br>
                <label for="capa">Capa:</label>
                <input type="file" name="capa" required><br>
                <label for="descricao">Descrição:</label>
                <input type="text" name="descricao" required><br>
                <input type="submit" value="submit">
            </form>`;
            var newWindow = window.open('', '_blank', 'width=400,height=400');
            newWindow.document.write(formHTML);
        }
    </script>
    <button class="btn"><a href="index_leitor.php" style="color:blue; text-decoration: none;">Voltar</a></button>
</body>
</html>