<?php
//LocalHost
/*$dbHost = 'Localhost';
$dbUsername = 'root';
$dbPassword = 'cacau69$dado';
$dbName = 'formulario-usuario';*/

//freesql
/*$dbHost = 'sql10.freesqldatabase.com';
$dbUsername = 'sql10623050';
$dbPassword = '2n8IPl2Gna';
$dbName = 'sql10623050';
$conexao = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);*/

//Azure
$conexao = mysqli_init();
mysqli_ssl_set($conexao,NULL,NULL, "DigiCertGlobalRootCA.crt.pem", NULL, NULL);
mysqli_real_connect($conexao, "banco-webteca.mysql.database.azure.com", "Gabriel", 
"Oliver#pato15", "WebtecaTables", 3306, MYSQLI_CLIENT_SSL);
?>