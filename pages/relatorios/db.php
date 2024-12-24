<?php
// Dados de acesso ao banco de dados
$host = "localhost";  // Endereço do servidor de banco de dados
$user = "root";       // Usuário do banco de dados
$password = "";       // Senha do banco de dados
$dbname = "sistema_erp";  // Nome do banco de dados

// Criação da conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verifica se ocorreu algum erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
