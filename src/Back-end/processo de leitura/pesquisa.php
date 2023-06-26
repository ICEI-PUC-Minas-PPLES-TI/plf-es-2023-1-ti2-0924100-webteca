<?php
session_start();
if(isset($_POST['submit']) && isset($_SESSION['Nome_Leitor'])){
    if($_POST['pesquisa'] == ""){
        //Caso o campo de pesquisa esteja vazio;
        echo "<script>alert('Campo de Pesquisa Vazio.');</script>";
        echo "<script>window.open('index_Leitor.php', '_self');</script>";
    }
    else{
        $pesquisa = trim($_POST["pesquisa"]);
        $livroPath = "livros/{$pesquisa}.html";//Pesquisa por meio do nome (html) do arquivo;
        if(file_exists($livroPath)){
            header("Location: {$livroPath}");//Redireciona o usuário para a pagina do livro;
            exit();
        }
        else{
            $livrosDir = "livros/";
            //Pega todos os htmls na pasta arquivo;
            $htmlFiles = glob($livrosDir . "*.html");
            //Pesquisa os arquivos html;
            foreach($htmlFiles as $file){
                //Lê o conteudo de cada html;
                $content = file_get_contents($file);
                //Pega o titulo do arquivo html;
                if(strpos($content, "<title>{$pesquisa}</title>") !== false){
                    //Caso o titulo seja igual ao input do usuário;
                    $fileName = basename($file);
                    header("Location: {$livrosDir}{$fileName}");
                    exit();
                }
            }
            //Caso a pesquisa por titulo não funcione;
            echo "<script>";
            echo "if(confirm('Livro não encontrado. Deseja solicitar este livro?'))";
            echo "  window.open('pedir_livro.html', '_self');";
            echo "else";
            echo "  window.open('index_Leitor.php', '_self');";
            echo "</script>";
        }
    }
}
else{
    echo "<script>alert('Erro! Usuário não logado.');</script>";
    echo "<script>window.open('index_Leitor.php', '_self');</script>";
}
?>