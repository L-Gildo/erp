<?php
session_start();
// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
  header("Location: /erp/pages/login/login.php");
  exit();
}

// Inicializa as variáveis de mensagem
$mensagemErro = '';
$mensagemSucesso = '';

// Configurações do banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sistema_erp";

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// Processar atualização do estoque
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateStock'])) {
  $codigo_produto = $_POST['codigo_produto'];
  $quantidade_atual = $_POST['quantidade_atual'];
  $quantidade_atualizada = $_POST['quantidade_atualizada'];
  $motivo_atualizacao = $_POST['motivo_atualizacao'];
  $data_atualizacao = date("Y-m-d H:i:s");

  // Inicia uma transação para garantir consistência
  $conn->begin_transaction();

  try {
    // Inserir na tabela "estoque"
    $stmt = $conn->prepare("INSERT INTO estoque (codigo_produto, quantidade_atual, quantidade_atualizada, motivo_atualizacao, data_atualizacao) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siiss", $codigo_produto, $quantidade_atual, $quantidade_atualizada, $motivo_atualizacao, $data_atualizacao);

    if (!$stmt->execute()) {
      throw new Exception("Erro ao inserir no estoque: " . $stmt->error);
    }

    // Atualizar a tabela "produtos"
    $stmtUpdate = $conn->prepare("UPDATE produtos SET quantidade_estoque = ? WHERE codigo = ?");
    $stmtUpdate->bind_param("ii", $quantidade_atualizada, $codigo_produto);

    if (!$stmtUpdate->execute()) {
      throw new Exception("Erro ao atualizar a tabela produtos: " . $stmtUpdate->error);
    }

    // Confirma a transação
    $conn->commit();
    $mensagemSucesso = "Estoque atualizado com sucesso!";
  } catch (Exception $e) {
    // Reverte a transação em caso de erro
    $conn->rollback();
    $mensagemErro = $e->getMessage();
  } finally {
    if (isset($stmt))
      $stmt->close();
    if (isset($stmtUpdate))
      $stmtUpdate->close();
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gerenciamento de Estoque</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      color: #333;
      padding: 20px;
    }

    h3 {
      margin: 20px auto;
      text-align: center;
    }

    .form-container {
      max-width: 2rw;
      margin: 0 auto;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    button {
      cursor: pointer;
      padding: 5px;
    }

    .form-container h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }

    .form-group input,
    .form-group textarea {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 20%;
    }

    .form-group textarea {
      resize: vertical;
    }

    .form-group2 {
      margin-bottom: 15px;
      width: 60%;
    }

    .form-group2 label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }

    .form-group2 input,
    .form-group2 textarea {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 100%;
    }

    .form-group2 textarea {
      resize: vertical;
    }


    .form-group3 {
      margin-bottom: 15px;
      width: 15%;
    }

    .form-group3 label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }

    .form-group3 input,
    .form-group3 textarea {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 100%;
    }

    .form-group3 textarea {
      resize: vertical;
    }

    .form-buttons {
      text-align: center;
      margin-top: 20px;
    }


    .form-buttons button {
      padding: 10px 20px;
      font-size: 16px;
      color: #fff;
      background-color: #4caf50;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin: 5px;
      transition: background 0.3s;
    }

    .form-buttons button:hover {
      background-color: #3d9241;
    }

    .product-table {
      margin-top: 20px;
      width: 100%;
      border-collapse: collapse;
    }

    .product-table th,
    .product-table td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    .product-table th {
      background-color: #f5f5f5;
      font-weight: bold;
    }

    .atualizacao-estoque {
      display: flex;
      justify-content: space-around;
    }

    .footer {
      text-align: center;
      padding: 10px 0;
      background-color: #f1f1f1;
      color: #333;
      position: fixed;
      bottom: 0;
      width: 100%;
      font-size: 12px;
      border-top: 1px solid #ccc;
    }

    @media (max-width: 600px) {
      .form-container {
        padding: 15px;
      }

      .form-buttons button {
        width: 100%;
      }
    }

    .icon-voltar img {
      width: 50px;
      position: absolute;
      right: 30px;
      top: 30px;
    }
  </style>
</head>

<body>
  <a href="/erp/dashboard.php" class="icon-voltar">
    <img src="/erp/images/home-button.png" title="Voltar ao Dashboard Laços e Papéis" alt="voltar" />
  </a>
  <div class="form-container">
    <h2>Gerenciamento de Estoque</h2>

    <!-- Mensagens de sucesso ou erro -->
    <?php if ($mensagemErro): ?>
      <p style="color: red;"><?php echo $mensagemErro; ?></p>
    <?php elseif ($mensagemSucesso): ?>
      <p style="color: green;"><?php echo $mensagemSucesso; ?></p>
    <?php endif; ?>

    <form id="stockForm">
      <div class="form-group">
        <label for="productCode">Código do Produto:</label>
        <input type="text" id="productCode" name="productCode" placeholder="Digite o código do produto" required />
        <button type="button" id="searchProduct">Pesquisar Produto</button>
      </div>

      <!-- Tabela com informações do produto -->
      <table id="productTable" class="product-table" style="display: none;">
        <thead>
          <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Categoria</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Cor</th>
            <th>Peso (kg)</th>
            <th>Preço (R$)</th>
            <th>Estoque</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td id="productCodeValue"></td>
            <td id="productName"></td>
            <td id="productDescription"></td>
            <td id="productCategory"></td>
            <td id="productBrand"></td>
            <td id="productModel"></td>
            <td id="productColor"></td>
            <td id="productWeight"></td>
            <td id="productPrice"></td>
            <td id="productStock"></td>
          </tr>
        </tbody>
      </table>
    </form>

    <!-- Formulário para atualizar o estoque -->
    <form method="POST" id="updateStockForm" style="display: none;">
      <h3>Atualizar Estoque</h3>
      <input type="hidden" name="codigo_produto" id="hiddenProductCode">
      <input type="hidden" name="quantidade_atual" id="hiddenProductStock">
      <section class="atualizacao-estoque">
        <div class="form-group3">
          <label for="quantidade_atualizada">Quantidade Atualizada:</label>
          <input type="number" name="quantidade_atualizada" id="quantidade_atualizada" required>
        </div>
        <div class="form-group2">
          <label for="motivo_atualizacao">Motivo da Atualização:</label>
          <textarea name="motivo_atualizacao" id="motivo_atualizacao" rows="4" required></textarea>
        </div>
      </section>
      <div class="form-buttons">
        <button type="submit" name="updateStock">Atualizar Estoque</button>
      </div>
    </form>
  </div>

  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> Sistema ERP Laços & Papéis - Todos os direitos reservados.</p>
  </footer>

  <script>
    document.getElementById('searchProduct').addEventListener('click', function () {
      const productCode = document.getElementById('productCode').value;

      if (productCode.trim() === '') {
        alert('Por favor, insira o código do produto.');
        return;
      }

      fetch('search_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ productCode })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('productTable').style.display = 'table';
            document.getElementById('updateStockForm').style.display = 'block';

            document.getElementById('productCodeValue').textContent = data.product.codigo;
            document.getElementById('productName').textContent = data.product.nome;
            document.getElementById('productDescription').textContent = data.product.descricao;
            document.getElementById('productCategory').textContent = data.product.categoria;
            document.getElementById('productBrand').textContent = data.product.marca;
            document.getElementById('productModel').textContent = data.product.modelo;
            document.getElementById('productColor').textContent = data.product.cor;
            document.getElementById('productWeight').textContent = data.product.peso;
            document.getElementById('productPrice').textContent = data.product.preco;
            document.getElementById('productStock').textContent = data.product.quantidade_estoque;

            // Preencher campos ocultos no formulário
            document.getElementById('hiddenProductCode').value = data.product.codigo;
            document.getElementById('hiddenProductStock').value = data.product.quantidade_estoque;
          } else {
            alert('Produto não encontrado!');
            document.getElementById('productTable').style.display = 'none';
            document.getElementById('updateStockForm').style.display = 'none';
          }
        })
        .catch(error => console.error('Erro na busca:', error));
    });
  </script>
</body>

</html>