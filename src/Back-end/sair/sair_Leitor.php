<?php
  session_start();
  unset($_SESSION['Nome_Leitor']);
  unset($_SESSION['Senha_Leitor']);
  header("Location: index.html");
?>