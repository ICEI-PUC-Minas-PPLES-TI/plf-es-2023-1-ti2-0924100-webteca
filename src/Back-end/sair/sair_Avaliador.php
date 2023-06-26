<?php
  session_start();
  unset($_SESSION['Nome_Avaliador']);
  unset($_SESSION['Senha_Avaliador']);
  header("Location: index.html");
?>