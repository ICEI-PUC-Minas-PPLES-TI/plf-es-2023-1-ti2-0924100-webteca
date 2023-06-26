<!DOCTYPE html>
<html lang="pt-br">
<html>
<head>
    <title>Notificações</title>
    <style>
    table {
        border-collapse: collapse;
        width: 100%;
        color: #588c7e;
        font-family: monospace;
        font-size: 25px;
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
            <th>ID-Notificação</th>
            <th>Descrição</th>
        </tr>
        <?php
        session_start();
        include_once('config.php');
        //Procura o ID_Leitor por meio do Nome_Leitor;
        $Nome_Leitor = $_SESSION['Nome_Leitor'];
        $query = "SELECT ID_Leitor FROM leitores WHERE Nome_Leitor = '$Nome_Leitor'";
        $result = mysqli_query($conexao, $query);
        $row = mysqli_fetch_assoc($result);
        $ID_Leitor = $row['ID_Leitor'];
        //Acessa o banco de dados e pega as notificações do usuário com base no seu ID;
        $query = "SELECT ID_Notificacao, Descricao FROM notificacao WHERE ID_Leitor = $ID_Leitor";
        $result = mysqli_query($conexao, $query);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>" . $row["ID_Notificacao"] . "</td>";
                echo "<td>" . $row["Descricao"] . "</td>";
                echo "</tr>";
            }
        }
        else{echo "<tr><td colspan='7'>0 Notificações!</td></tr>";}
        ?>
    </table>
    <button class="btn"><a href="index_Leitor.php" style="color:blue; text-decoration: none;">Voltar</a></button>
</body>
</html>