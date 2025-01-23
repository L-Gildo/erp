<?php
header('Content-Type: application/json');

// Configurações do banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sistema_erp";

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
      echo json_encode(['success' => false, 'error' => 'Falha na conexão ao banco de dados.']);
      exit();
}

// Receber dados da requisição
$data = json_decode(file_get_contents('php://input'), true);
$productCode = $data['productCode'] ?? '';

if (empty($productCode)) {
      echo json_encode(['success' => false, 'error' => 'Código do produto não fornecido.']);
      exit();
}

// Consultar o produto no banco de dados
$sql = "SELECT * FROM produtos WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productCode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
      $product = $result->fetch_assoc();
      echo json_encode(['success' => true, 'product' => $product]);
} else {
      echo json_encode(['success' => false, 'error' => 'Produto não encontrado.']);
}

$stmt->close();
$conn->close();
