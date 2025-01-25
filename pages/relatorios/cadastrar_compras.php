<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
  header("Location: /erp/pages/login/login.php");
  exit();
}

// Configurações do banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sistema_erp";

// Variáveis para mensagens
$mensagemErro = null;
$mensagemSucesso = null;

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// Função para validar os dados do formulário
function validarDados($dados)
{
  return isset($dados) && !empty(trim($dados));
}

// Processar o formulário ao ser submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nomeFornecedor = $_POST['supplierName'] ?? null;
  $cnpj = $_POST['supplierCNPJ'] ?? null;
  $dataCompra = $_POST['purchaseDate'] ?? null;
  $dataEntrega = $_POST['deliveryDate'] ?? null;
  $formaPagamento = $_POST['paymentMethod'] ?? null;
  $parcelas = $_POST['installments'] ?? null;
  $notas = $_POST['purchaseNotes'] ?? null;
  $itens = $_POST['itemDescription'] ?? [];
  $quantidades = $_POST['itemQuantity'] ?? [];
  $precos = $_POST['itemPrice'] ?? [];

  // Validações
  if (!validarDados($nomeFornecedor) || !validarDados($cnpj) || !validarDados($dataCompra) || !validarDados($formaPagamento) || empty($itens)) {
    $mensagemErro = "Por favor, preencha todos os campos obrigatórios.";
  } elseif (count($itens) !== count($quantidades) || count($itens) !== count($precos)) {
    $mensagemErro = "Os itens, quantidades e preços devem estar alinhados.";
  } else {
    // Inserir a compra no banco de dados
    $sqlCompra = "INSERT INTO compras (fornecedor_nome, fornecedor_cnpj, data_compra, data_entrega, forma_pagamento, parcelas, observacoes) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtCompra = $conn->prepare($sqlCompra);
    $stmtCompra->bind_param(
      "sssssis",
      $nomeFornecedor,
      $cnpj,
      $dataCompra,
      $dataEntrega,
      $formaPagamento,
      $parcelas,
      $notas
    );

    if ($stmtCompra->execute()) {
      $compraId = $stmtCompra->insert_id; // ID da compra recém-criada

      // Inserir os itens da compra
      $sqlItem = "INSERT INTO itenscompra (compra_id, descricao, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
      $stmtItem = $conn->prepare($sqlItem);

      foreach ($itens as $index => $descricao) {
        $quantidade = $quantidades[$index];
        $preco = $precos[$index];

        if (!is_numeric($quantidade) || !is_numeric($preco)) {
          $mensagemErro = "Quantidade e preço devem ser valores numéricos.";
          break;
        }

        $stmtItem->bind_param("isid", $compraId, $descricao, $quantidade, $preco);
        $stmtItem->execute();
      }

      if (!$mensagemErro) {
        $mensagemSucesso = "Compra cadastrada com sucesso!";
      }
    } else {
      $mensagemErro = "Erro ao cadastrar a compra: " . $stmtCompra->error;
    }

    $stmtCompra->close();
  }
}

// Buscar fornecedores para autocomplete
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['searchSupplier'])) {
  $search = $conn->real_escape_string($_GET['searchSupplier']);
  $sql = "SELECT nome, cnpj FROM fornecedores WHERE nome LIKE ? LIMIT 10";
  $stmt = $conn->prepare($sql);
  $searchTerm = "%$search%";
  $stmt->bind_param("s", $searchTerm);
  $stmt->execute();
  $result = $stmt->get_result();

  $fornecedores = [];
  while ($row = $result->fetch_assoc()) {
    $fornecedores[] = $row;
  }

  echo json_encode($fornecedores);
  exit();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro de Compras</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f9;
    }

    .form-container {
      max-width: 800px;
      margin: 50px auto;
      padding: 20px;
      background: #ffffff;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border-radius: 8px;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }

    .form-actions {
      text-align: center;
      margin-top: 20px;
    }

    .form-actions button {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      background-color: #28a745;
      color: #fff;
      cursor: pointer;
      font-size: 16px;
    }

    .form-actions button:hover {
      background-color: #218838;
    }

    .form-row {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .form-row .form-group {
      flex: 1;
    }

    .autocomplete-list {
      list-style: none;
      margin: 0;
      padding: 0;
      border: 1px solid #f4f4f9;
      max-height: 150px;
      overflow-y: auto;
      position: relative;
      background: #fff;
      z-index: 10;
      width: 100%;
    }

    .autocomplete-list li {
      padding: 8px;
      cursor: pointer;
    }

    .autocomplete-list li:hover {
      background: #f0f0f0;
    }

    @media (max-width: 768px) {
      .form-row {
        flex-direction: column;
      }
    }

    .icon-voltar img {
      width: 50px;
      position: absolute;
      right: 30px;
      top: 30px;
    }

    .success-message {
      color: green;
      background-color: #e6ffe6;
      padding: 10px;
      border: 1px solid green;
      margin-bottom: 15px;
      border-radius: 5px;
    }

    .error-message {
      color: red;
      background-color: #ffe6e6;
      padding: 10px;
      border: 1px solid red;
      margin-bottom: 15px;
      border-radius: 5px;
      text-align: center;
    }
  </style>
</head>

<body>
  <a href="/erp/dashboard.php" class="icon-voltar">
    <img src="/erp/images/home-button.png" title="Voltar ao Dashboard Laços e papéis" alt="voltar" />
  </a>
  <div class="form-container">
    <h2>Cadastro de Compras</h2>

    <!-- Mensagens -->
    <?php if ($mensagemSucesso): ?>
      <div class="success-message"><?php echo htmlspecialchars($mensagemSucesso); ?></div>
    <?php endif; ?>
    <?php if ($mensagemErro): ?>
      <div class="error-message"><?php echo htmlspecialchars($mensagemErro); ?></div>
    <?php endif; ?>

    <form method="POST" id="purchaseForm">
      <h3>Informações do Fornecedor</h3>
      <div class="form-group">
        <div class="form-group">
          <label for="supplierName">Nome do Fornecedor:</label>
          <input type="text" id="supplierName" name="supplierName" required oninput="fetchSupplierData()" />
          <ul id="supplierSuggestions" class="autocomplete-list"></ul>
        </div>
        <div class="form-group">
          <label for="supplierCNPJ">CNPJ:</label>
          <input type="text" id="supplierCNPJ" name="supplierCNPJ" readonly />
        </div>


        <h3>Informações da Compra</h3>
        <div class="form-row">
          <div class="form-group">
            <label for="purchaseDate">Data da Compra:</label>
            <input type="date" id="purchaseDate" name="purchaseDate" required />
          </div>
          <div class="form-group">
            <label for="deliveryDate">Data de Entrega:</label>
            <input type="date" id="deliveryDate" name="deliveryDate" />
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="paymentMethod">Forma de Pagamento:</label>
            <select id="paymentMethod" name="paymentMethod" required>
              <option value="boleto">Boleto</option>
              <option value="cartao">Cartão</option>
              <option value="transferencia">Transferência Bancária</option>
            </select>
          </div>
          <div class="form-group">
            <label for="installments">Parcelas:</label>
            <input type="number" id="installments" name="installments" min="1" max="12" />
          </div>
        </div>

        <h3>Itens da Compra</h3>
        <div id="itemsContainer">
          <div class="form-row item-group">
            <div class="form-group">
              <label>Descrição:</label>
              <input type="text" name="itemDescription[]" required />
            </div>
            <div class="form-group">
              <label>Quantidade:</label>
              <input type="number" name="itemQuantity[]" required />
            </div>
            <div class="form-group">
              <label>Preço Unitário:</label>
              <input type="number" name="itemPrice[]" step="0.01" required />
            </div>
          </div>
        </div>
        <div class="form-actions">
          <button type="button" onclick="addItem()">Adicionar Item</button>
        </div>

        <h3>Observações</h3>
        <div class="form-group">
          <label for="purchaseNotes">Notas adicionais:</label>
          <textarea id="purchaseNotes" name="purchaseNotes"></textarea>
        </div>

        <div class="form-actions">
          <button type="submit">Salvar Compra</button>
        </div>
    </form>
  </div>

  <script>
    function addItem() {
      const itemsContainer = document.getElementById("itemsContainer");
      const newItemGroup = document.createElement("div");
      newItemGroup.classList.add("form-row", "item-group");
      newItemGroup.innerHTML = `
      <div class="form-group">
        <label>Descrição:</label>
        <input type="text" name="itemDescription[]" required />
      </div>
      <div class="form-group">
        <label>Quantidade:</label>
        <input type="number" name="itemQuantity[]" required />
      </div>
      <div class="form-group">
        <label>Preço Unitário:</label>
        <input type="number" name="itemPrice[]" step="0.01" required />
      </div>`;
      itemsContainer.appendChild(newItemGroup);
    }
  </script>

  <script>
    async function fetchSupplierData() {
      const input = document.getElementById('supplierName');
      const suggestions = document.getElementById('supplierSuggestions');
      const cnpjField = document.getElementById('supplierCNPJ');
      const query = input.value;

      // Limpa as sugestões e o CNPJ
      suggestions.innerHTML = '';
      cnpjField.value = '';

      if (query.length > 2) {
        try {
          const response = await fetch(`/erp/pages/relatorios/getSuppliers.php?q=${query}`);
          const suppliers = await response.json();

          // Renderiza as sugestões
          suppliers.forEach((supplier) => {
            const listItem = document.createElement('li');
            listItem.textContent = supplier.nome;
            listItem.onclick = () => {
              input.value = supplier.nome;
              cnpjField.value = supplier.cnpj;
              suggestions.innerHTML = ''; // Limpa as sugestões
            };
            suggestions.appendChild(listItem);
          });
        } catch (error) {
          console.error('Erro ao buscar fornecedores:', error);
        }
      }
    }
  </script>

</body>

</html>