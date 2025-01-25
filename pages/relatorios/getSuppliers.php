<?php
// Configurações do banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sistema_erp";

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
      die("Falha na conexão: " . $conn->connect_error);
}

// Obter o termo de busca
$q = $_GET['q'] ?? '';

if ($q) {
      $sql = "SELECT nome, cnpj FROM fornecedores WHERE nome LIKE ? LIMIT 10";
      $stmt = $conn->prepare($sql);
      $searchTerm = '%' . $q . '%';
      $stmt->bind_param('s', $searchTerm);
      $stmt->execute();
      $result = $stmt->get_result();

      $suppliers = [];
      while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
      }

      echo json_encode($suppliers);
}

$conn->close();
?>