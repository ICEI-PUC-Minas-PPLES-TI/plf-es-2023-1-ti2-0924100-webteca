<?php
if(isset($_POST['submit'])){
    include_once('config.php');
    //Valores do leitor;
    $CPF = $_POST['CPF'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $pagamento = $_POST['pagamento'];
    //Valores do Cartão;
    $numcartao = $_POST['numcartao'];
    $titular = $_POST['titular'];
    $valor = $_POST['valor'];
    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $data = date('Y-m-d H:i:s');//Pega a hora e a data atual;
    //Verifica se é uma nova conta ou uma que já existe;
    $query = "SELECT * FROM leitores WHERE CPF_Leitor = '$CPF' OR Nome_Leitor = '$nome' 
    OR Email_Leitor = '$email' OR Senha_Leitor = '$senha'";
    $result = mysqli_query($conexao, $query);
    $count = mysqli_num_rows($result);
    if($count > 0){
        if($pagamento == 'credito'){
            echo "<script>alert('Você já tem uma Conta Paga!');</script>";
            echo "<script>window.open('credito.html', '_self');</script>";
            die();
        }
        //Conta de usuário já existente (fazer update da conta);
        $pagamento = 'credito';
        $updateQuery = "UPDATE leitores SET Pagamento = '$pagamento' WHERE Nome_Leitor = '$nome'";
        $updateResult = mysqli_query($conexao, $updateQuery);
        //Criar cartão;
        $insertCartaoQuery = "INSERT INTO cartao_credito (Numero_CartaoCredito, Titular, Mes_Expiracao, Ano_Expiracao, Data_Pagamento, Valor_Pagamento, ID_Leitor)
        VALUES ('$numcartao', '$titular', $mes, $ano, '$data', $valor, (SELECT ID_Leitor FROM leitores WHERE Nome_Leitor = '$nome'))";
        $insertCartaoResult = mysqli_query($conexao, $insertCartaoQuery);
        if($insertCartaoResult && $updateResult){
            header("Location: index_Leitor.php");
            die();
        } 
        else{
            echo "<script>alert('Erro ao inserir os dados no banco!');</script>";
            echo "<script>window.open('credito.html', '_self');</script>";
            die();
        }
    }
    else{
        //Nova conta (criar conta);
        $insertQuery = "INSERT INTO leitores (CPF_Leitor, Nome_Leitor, Email_Leitor, Senha_Leitor, Pagamento) 
        VALUES ('$CPF', '$nome', '$email', '$senha', '$pagamento')";
        $insertResult = mysqli_query($conexao, $insertQuery);
        //Criar cartão;
        $insertCartaoQuery = "INSERT INTO cartao_credito (Numero_CartaoCredito, Titular, Mes_Expiracao, Ano_Expiracao, Data_Pagamento, Valor_Pagamento, ID_Leitor)
        VALUES ('$numcartao', '$titular', $mes, $ano, '$data', $valor, (SELECT ID_Leitor FROM leitores WHERE Nome_Leitor = '$nome'))";
        $insertCartaoResult = mysqli_query($conexao, $insertCartaoQuery);
        if($insertResult && $insertCartaoResult){
            header("Location: login.html");
            die();
        } 
        else{
            echo "<script>alert('Erro ao inserir os dados no banco!');<script>";
            echo "<script>window.open('credito.html', '_self');</script>";
            die();
        }
    }
}
?>