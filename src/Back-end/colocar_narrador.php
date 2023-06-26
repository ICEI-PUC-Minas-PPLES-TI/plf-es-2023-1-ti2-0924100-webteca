<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Inserir Narrador</title>
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
        font-size: 18px;
        text-align: left;
    }
    th {
        background-color: #588c7e;
        color: white;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
    .btn {
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
            <th>ID-Currículo</th>
            <th>Nome do Leitor</th>
            <th>Currículo</th>
            <th>Data de Envio</th>
            <th>Data de Resposta</th>
            <th>Tempo Gasto</th>
            <th>Resposta</th>
            <th>Ação</th>
        </tr>
        <?php
        include_once('config.php');
        //Acessa o banco de dados e pega os curriculos existentes;
        $query = "SELECT ID_Curriculo, ID_Leitor, Arquivo, Data_Envio, Data_Resposta, Data_Dif, Resposta 
        FROM curriculo";
        $result = mysqli_query($conexao, $query);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>" . $row["ID_Curriculo"] . "</td>";
                //Usa ID_Leitor para pegar o Nome_Leitor;
                $leitorQuery = "SELECT Nome_Leitor FROM leitores WHERE ID_Leitor = " . $row["ID_Leitor"];
                $leitorResult = mysqli_query($conexao, $leitorQuery);
                $leitorRow = mysqli_fetch_assoc($leitorResult);
                echo "<td>" . $leitorRow["Nome_Leitor"] . "</td>";
                echo "<td><a href='download_curriculo.php?id=" . $row["ID_Curriculo"] . "'>Download File</a></td>";
                echo "<td>" . $row["Data_Envio"] . "</td>";
                echo "<td>" . $row["Data_Resposta"] . "</td>";
                echo "<td>" . $row["Data_Dif"] . "</td>";
                echo "<td>" . $row["Resposta"] . "</td>";
                echo "<td>";
                echo "<form action='update_curriculo.php' method='POST'>";//Acessa o update_curriculo;
                echo "<input type='hidden' name='curriculo_id' value='" . $row["ID_Curriculo"] . "'/>";
                echo "<button type='submit' name='action' value='aprovar' onclick='disableButtons(this.parentNode.getElementsByTagName(\"button\"))'>Aprovar</button>";
                echo "<button type='submit' name='action' value='rejeitar' onclick='disableButtons(this.parentNode.getElementsByTagName(\"button\"))'>Rejeitar</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } 
        else{echo "<tr><td colspan='7'>0 currículos</td></tr>";}
        ?>
    </table>
    <?php
    include_once('config.php');
    $query = "SELECT Data_Dif FROM curriculo WHERE Data_Dif IS NOT NULL";
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
    else{echo "<p>Nenhum Currículo/Pedido foi avaliado ainda.</p>";}
    ?>
    <button class="btn"><a href="index_Avaliador.php" style="color:blue; text-decoration: none;">Voltar</a></button>
</body>
</html>