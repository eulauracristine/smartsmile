<?php
// Configurações do Banco de Dados
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Substitua pelo seu usuário MySQL
define('DB_PASSWORD', 'Js.27112022');     // Substitua pela sua senha MySQL
define('DB_NAME', 'bdSmartSmile');

// Tentativa de conexão com o banco de dados MySQL
$link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Checar conexão
if ($link === false) {
    die("ERRO: Não foi possível conectar ao MySQL. " . $link->connect_error);
}

// Configurar o conjunto de caracteres para UTF8
$link->set_charset("utf8");
?>