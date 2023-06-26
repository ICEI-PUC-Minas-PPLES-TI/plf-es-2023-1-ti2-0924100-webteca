<?php
include_once('config.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    
    //Pega o arquivo da database através do ID fornecido;
    $query = "SELECT Arquivo FROM curriculo WHERE ID_Curriculo = $id";
    $result = mysqli_query($conexao, $query);
    
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $fileContent = $row["Arquivo"];
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');//Muda o tipo do arquivo caso não seja PDF;
        header('Content-Disposition: attachment; filename=' . $id . '.pdf');//Muda o nome do arquivo caso necessário;
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($fileContent));
        
        ob_clean();
        flush();
        echo $fileContent;
        exit;
    }
}
?>