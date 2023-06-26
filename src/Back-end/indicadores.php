<?php
include_once('config.php');
//Tempo de resposta do Credenciamento de narrador;
$queryCredenciamento = "SELECT WEEK(Data_Envio) AS Week, AVG(TIME_TO_SEC(Data_Dif)) AS AverageTime 
FROM curriculo WHERE Data_Dif IS NOT NULL GROUP BY WEEK(Data_Envio)";
$resultCredenciamento = mysqli_query($conexao, $queryCredenciamento);
$labelsCredenciamento = array();
$dataCredenciamento = array();
while ($row = $resultCredenciamento->fetch_assoc()) {
  $week = $row["Week"];
  $labelsCredenciamento[] = "Week " . $week;
  $dataCredenciamento[] = $row["AverageTime"];
}
//Tempo de postagem de narração;
$queryPostagem = "SELECT MONTH(Data_Envio) AS Month, AVG(TIME_TO_SEC(Data_Dif)) AS AverageTime 
FROM pedido_livro WHERE Data_Dif IS NOT NULL GROUP BY MONTH(Data_Envio)";
$resultPostagem = mysqli_query($conexao, $queryPostagem);
$labelsPostagem = array();
$dataPostagem = array();
while ($row = $resultPostagem->fetch_assoc()) {
  $month = date("F", mktime(0, 0, 0, $row["Month"], 1));//Tranforma o número do mês para o seu nome;
  $labelsPostagem[] = $month;
  $dataPostagem[] = $row["AverageTime"];
}
//Tempo de respota do pedido de remoção;
$queryRemocao = "SELECT WEEK(Data_Envio) AS Week, AVG(TIME_TO_SEC(Data_Dif)) AS AverageTime 
FROM pedido_remocao WHERE Data_Dif IS NOT NULL GROUP BY WEEK(Data_Envio)";
$resultRemocao = mysqli_query($conexao, $queryRemocao);
$labelsRemocao = array();
$dataRemocao = array();
while ($row = $resultRemocao->fetch_assoc()) {
  $week = $row["Week"];
  $labelsRemocao[] = "Week " . $week;
  $dataRemocao[] = $row["AverageTime"];
}
//Valor arrecadado;
$queryValor = "SELECT MONTH(Data_Pagamento) AS Month, SUM(Valor_Pagamento) AS TotalValor 
FROM cartao_credito GROUP BY MONTH(Data_Pagamento)";
$resultValor = mysqli_query($conexao, $queryValor);
$labelsValor = array();
$dataValor = array();
$totalValor = 0;
while ($row = $resultValor->fetch_assoc()) {
  $month = date("F", mktime(0, 0, 0, $row["Month"], 1));
  $labelsValor[] = $month;
  $dataValor[] = $row["TotalValor"];
  $totalValor += $row["TotalValor"];//Acumula o total de dinheiro;
}
//Taxa de Livros removidos;
$queryCancelamento = "SELECT DAY(Data_Envio) AS DAY, COUNT(*) AS countAceito FROM pedido_remocao
WHERE Resposta = 'Aceito' GROUP BY DAY(Data_Envio)";
$resultCancelamento = mysqli_query($conexao, $queryCancelamento);
$labelsCancelamento = array();
$dataCancelamento = array();
$queryTotalLivros = "SELECT COUNT(*) AS total FROM livros";//Pega o número total de livros;

$resultTotalLivros = mysqli_query($conexao, $queryTotalLivros);
$rowTotalLivros = $resultTotalLivros->fetch_assoc();
$totalLivros = $rowTotalLivros['total'];

while ($row = $resultCancelamento->fetch_assoc()) {
  $day = $row['DAY'];
  $labelsCancelamento[] = $day;
  //Pega o total de pedidos com Resposta = 'Aceito' para cada dia;
  $countAceito = $row['countAceito'];
  $percentage = ($countAceito / ($countAceito + $totalLivros)) * 100;//Calcula a porcentagem;
  $dataCancelamento[] = $percentage;
}
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Indicadores de Desempenho</title>
    <!-- CSS -->
    <style>
      * {
        margin: 0;
        padding: 0;
        font-family: sans-serif;
      }
      .chartMenu {
        width: 100vw;
        height: 40px;
        background: #1A1A1A;
        color: rgba(54, 162, 235, 1);
      }
      .chartMenu p {
        padding: 10px;
        font-size: 20px;
      }
      .chartCard {
        width: 100vw;
        height: calc(100vh - 40px);
        background: rgba(54, 162, 235, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .chartBox {
        width: 700px;
        padding: 20px;
        border-radius: 20px;
        border: solid 3px rgba(54, 162, 235, 1);
        background: white;
      }
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
      h1{
        background-color: pink;
        text-align: center;
      }
      h2{
        background-color: pink;
        text-align: center;
      }
    </style>
  </head>
  <body>
    <div class="chartMenu">
      <p>WWW.CHARTJS3.COM (Chart JS 4.1.2)</p>
    </div>
    <!-- JavaScript para os grafos -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
    <h1>Indicadores de Desempenho</h1>
    <!-- Tempo de resposta do Credenciamento de narrador -->
    <h2>Tempo de resposta do Credenciamento de narrador (Semanal)</h2>
    <div class="chartCard">
      <div class="chartBox">
        <canvas id="tabela_credenciamento"></canvas>
      </div>
    </div>
    <script>
      /*O valor da semana exibido é determinado com base no sistema de numeração de semanas ISO-8601, 
      que define que uma semana começa na segunda-feira e que a primeira semana do ano é 
      aquela que contém a primeira quinta-feira. O número da semana representa a semana dentro do ano.
      Logo a primeira semana do ano recebe o valor 1 e assim por diante*/
      const labelsCredenciamento = <?php echo json_encode($labelsCredenciamento); ?>;//Semana em que os pedidos foram enviados;
      const dataCredenciamento = <?php echo json_encode($dataCredenciamento); ?>;//Média do tempo de resposta;
      const ctxCredenciamento = document.getElementById('tabela_credenciamento');
      new Chart(ctxCredenciamento, {
        type: 'bar',
        data: {
          labels: labelsCredenciamento,
          datasets: [{
            label: 'Média do Tempo de Resposta do Pedido de Credenciamento de Narrador (em Segundos).',
            data: dataCredenciamento,
            borderWidth: 1
          }]
        },
        options: {scales: {y: {beginAtZero: true}}}
      });
    </script>
    <!-- End - Tempo de resposta do Credenciamento de narrador -->
    <!-- Tempo de postagem de narração -->
    <h2>Tempo de postagem de narração (Mensal)</h2>
    <div class="chartCard">
      <div class="chartBox">
        <canvas id="tabela_postagemlivro"></canvas>
      </div>
    </div>
    <script>
      const labelsPostagem = <?php echo json_encode($labelsPostagem); ?>;//Mêses em que os pedidos foram enviados;
      const dataPostagem = <?php echo json_encode($dataPostagem); ?>;//Média do tempo de resposta;
      const ctxPostagem = document.getElementById('tabela_postagemlivro');
      new Chart(ctxPostagem, {
        type: 'bar',
        data: {
          labels: labelsPostagem,
          datasets: [{
            label: 'Média do Tempo de Postagem de Narração (em Segundos).',
            data: dataPostagem,
            borderWidth: 1
          }]
        },
        options: {scales: {y: {beginAtZero: true}}}
      });
    </script>
    <!-- End - Tempo de postagem de narração -->
    <!-- Tempo de respota do pedido de remoção -->
    <h2>Tempo de respota do pedido de remoção (Semanal)</h2>
    <div class="chartCard">
      <div class="chartBox">
        <canvas id="tabela_remocaolivro"></canvas>
      </div>
    </div>
    <script>
      const labelsRemocao = <?php echo json_encode($labelsRemocao); ?>;//Semana em que os pedidos foram enviados;
      const dataRemocao = <?php echo json_encode($dataRemocao); ?>;//Média do tempo de resposta;
      const ctxRemocao = document.getElementById('tabela_remocaolivro');
      new Chart(ctxRemocao, {
        type: 'bar',
        data: {
          labels: labelsRemocao,
          datasets: [{
            label: 'Média do Tempo de Resposta do pedido de Remoção (em Segundos).',
            data: dataRemocao,
            borderWidth: 1
          }]
        },
        options: {scales: {y: {beginAtZero: true}}}
      });
    </script>
    <!-- End - Tempo de respota do pedido de remoção -->
    <!-- Valor arrecadado -->
    <h2>Valor arrecadado (Mensal)</h2>
    <div class="chartCard">
      <div class="chartBox">
        <canvas id="tabela_valorarrecadado"></canvas>
      </div>
    </div>
    <script>
      const labelsValor = <?php echo json_encode($labelsValor); ?>;
      const dataValor = <?php echo json_encode($dataValor); ?>;
      const ctxValor = document.getElementById('tabela_valorarrecadado');
      const totalValor = <?php echo $totalValor; ?>;
      new Chart(ctxValor, {
        type: 'bar',
        data: {
          labels: labelsValor,
          datasets: [{
            label: 'Total de dinheiro arrecadado (em Reais).',
            data: dataValor,
            borderWidth: 1
          }]
        },
        options: {scales: {y: {beginAtZero: true,
          callback: function(value) {
            return 'R$ ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }}},
          plugins: {title: {display: true,
          text: 'Valor arrecadado (Mensal)',
          font: {
            size: 18
          }}, subtitle: {display: true,
          text: 'Total: R$ ' + totalValor.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'),
          font: {size: 14}}}
        }
      });
    </script>
    <!-- End Valor arrecadado -->
    <!-- Taxa de Livros removidos -->
    <h2>Taxa de Livros removidos (Diário)</h2>
    <div class="chartCard">
      <div class="chartBox">
        <canvas id="tabela_taxacancelamento"></canvas>
      </div>
    </div>
    <script>
      const labelsCancelamento = <?php echo json_encode($labelsCancelamento); ?>;
      const dataCancelamento = <?php echo json_encode($dataCancelamento); ?>;
      const ctxCancelamento = document.getElementById('tabela_taxacancelamento');
      new Chart(ctxCancelamento, {
        type: 'bar',
        data: {
          labels: labelsCancelamento,
          datasets: [{
            label: 'Taxa de Livros removidos (% de livros removidos).',
            data: dataCancelamento,
            borderWidth: 1
          }]
        },
        options: {scales: {y: {beginAtZero: true}}}
      });
    </script>
    <!-- End - Taxa de Livros removidos -->
    <button class="btn"><a href="index_Avaliador.php" style="color:blue; text-decoration: none;">Voltar</a></button>
  </body>
</html>