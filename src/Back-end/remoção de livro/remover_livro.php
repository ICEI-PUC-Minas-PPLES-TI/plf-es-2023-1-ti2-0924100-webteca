<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Remover Livro</title>
    <script>
        function disableButtons(buttons){
            buttons.forEach(function (button){
                button.disabled = true;
                button.innerHTML = 'Clicked';
            });
        }
    </script>
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
            <th>Nome do Livro</t>
            <th>Motivo</th>
            <th>Data de Envio</th>
            <th>Data de Resposta</th>
            <th>Tempo Gasto</th>
            <th>Resposta</th>
            <th>Ação</th>
        </tr>
        <?php
        include_once('config.php');
        $query = "SELECT ID_PedidoRemocao, Nome_Livro, Motivo, 
        Data_Envio, Data_Resposta, Data_Dif, Resposta FROM pedido_remocao";
        $result = mysqli_query($conexao, $query);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>" . $row["Nome_Livro"] . "</td>";
                echo "<td>" . $row["Motivo"] . "</td>";
                echo "<td>" . $row["Data_Envio"] . "</td>";
                echo "<td>" . $row["Data_Resposta"] . "</td>";
                echo "<td>" . $row["Data_Dif"] . "</td>";
                echo "<td>" . $row["Resposta"] . "</td>";
                echo "<td>";
                echo "<form action='delete_livro.php' method='POST'>";//Acessa o delete_livro;
                echo "<input type='hidden' name='pedidoremocao_id' value='" . $row["ID_PedidoRemocao"] . "'/>";
                echo "<button type='submit' name='action' value='aceitar' onclick='disableButtons(this.parentNode.getElementsByTagName(\"button\"))'>Aceitar</button>";
                echo "<button type='submit' name='action' value='recusar' onclick='disableButtons(this.parentNode.getElementsByTagName(\"button\"))'>Recusar</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        }
        else{echo "<tr><td colspan='7'>0 Pedidos</td></tr>";}
        ?>
    </table>
    <?php
    include_once('config.php');
    $query = "SELECT Data_Dif FROM pedido_remocao WHERE Data_Dif IS NOT NULL";
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
    else{echo "<p>Nenhum Pedido de remoção foi avaliado ainda.</p>";}
    ?>
    <button class="btn"><a href="index_Avaliador.php" style="color:blue; text-decoration: none;">Voltar</a></button>
</body>
</html>