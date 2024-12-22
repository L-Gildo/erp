<?php
session_start();

// Definir parâmetros de conexão
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sistema_erp";

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
} else {
    echo "Conexão bem-sucedida ao banco de dados!";
}
?>
