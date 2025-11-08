<?php
$host = "localhost";
$user = "root";
$pass = "Lcs14hmhm@";
$db   = "bdSmartSmile"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
