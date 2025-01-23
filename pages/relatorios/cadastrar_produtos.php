<?php
session_start();
// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
  // Redireciona para a página de login se o usuário não estiver logado
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

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = $_POST['productName'];
  $descricao = $_POST['productDescription'];
  $codigo = $_POST['productCode'];
  $categoria = $_POST['productCategory'];
  $marca = $_POST['productBrand'];
  $modelo = $_POST['productModel'];
  $cor = $_POST['productColor'];
  $peso = $_POST['productWeight'];
  $preco = $_POST['productPrice'];
  $quantidade_estoque = $_POST['productStock'];

  // Inserir o produto no banco de dados
  $sql = "INSERT INTO produtos 
            (nome, descricao, codigo, categoria, marca, modelo, cor, peso, preco, quantidade_estoque) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "ssssssssdi",
    $nome,
    $descricao,
    $codigo,
    $categoria,
    $marca,
    $modelo,
    $cor,
    $peso,
    $preco,
    $quantidade_estoque,
  );

  if ($stmt->execute()) {
    $mensagemSucesso = "Produto cadastrado com sucesso!";
  } else {
    $mensagemErro = "Erro ao cadastrar o produto: " . $stmt->error;
  }

  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro de Produto</title>
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

    .form-container {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .form-container h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 15px;
    }

    .form-group label {
      margin-bottom: 5px;
      font-weight: bold;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 100%;
    }

    .form-group textarea {
      resize: vertical;
    }

    .form-group-row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }

    .form-group-row .form-group {
      flex: 1;
      min-width: calc(50% - 15px);
    }

    .form-buttons {
      text-align: center;
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
      .form-group-row .form-group {
        min-width: 100%;
      }

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
    <img src="/erp/images/home-button.png" title="Voltar ao Dashboard Laços e papéis" alt="voltar" /></a>
  <div class="form-container">
    <h2>Cadastro de Produto</h2>

    <!-- Exibir mensagens, se existirem -->
    <?php if ($mensagemSucesso): ?>
      <div class="success-message">
        <?php echo htmlspecialchars($mensagemSucesso); ?>
      </div>
    <?php endif; ?>

    <?php if ($mensagemErro): ?>
      <div class="error-message">
        <?php echo htmlspecialchars($mensagemErro); ?>
      </div>
    <?php endif; ?>

    <form action="" method="POST">
      <!-- Informações básicas -->
      <div class="form-group">
        <label for="productName">Nome do Produto:</label>
        <input type="text" id="productName" name="productName" placeholder="Ex: Smartphone XYZ" required />
      </div>
      <div class="form-group">
        <label for="productDescription">Descrição:</label>
        <textarea id="productDescription" name="productDescription" rows="4"
          placeholder="Detalhes do produto"></textarea>
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="productCode">Código:</label>
          <input type="text" id="productCode" name="productCode" placeholder="Ex: 12345" required />
        </div>
        <div class="form-group">
          <label for="productCategory">Categoria:</label>
          <select id="productCategory" name="productCategory" required>
            <option value="">Selecione</option>
            <option value="eletronicos">Eletrônicos</option>
            <option value="moda">Moda</option>
            <option value="degustacao">Degustação</option>
            <option value="utilitarios">Utilitários</option>
            <option value="aluguel">Peça de aluguel</option>
            <option value="papelaria">Papelaria</option>
            <option value="outros">Outros</option>
          </select>
        </div>
      </div>
      <!-- Especificações -->
      <div class="form-group-row">
        <div class="form-group">
          <label for="productBrand">Marca:</label>
          <input type="text" id="productBrand" name="productBrand" placeholder="Ex: Samsung" />
        </div>
        <div class="form-group">
          <label for="productModel">Modelo:</label>
          <input type="text" id="productModel" name="productModel" placeholder="Ex: Galaxy S21" />
        </div>
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="productColor">Cor:</label>
          <input type="text" id="productColor" name="productColor" placeholder="Ex: Preto" />
        </div>
        <div class="form-group">
          <label for="productWeight">Peso (Kg):</label>
          <input type="number" id="productWeight" name="productWeight" step="0.01" placeholder="Ex: 1.5" />
        </div>
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="productPrice">Preço:</label>
          <input type="number" id="productPrice" name="productPrice" step="0.01" placeholder="Ex: 999.99" required />
        </div>
        <div class="form-group">
          <label for="productStock">Quantidade em Estoque:</label>
          <input type="number" id="productStock" name="productStock" placeholder="Ex: 50" required />
        </div>
      </div>
      <!-- Botões -->
      <div class="form-buttons">
        <button type="submit">Salvar</button>
        <button type="reset">Limpar</button>
      </div>
    </form>
  </div>
  <!-- Rodapé -->
  <footer class="footer">
    <p>
      &copy;
      <?php echo date("Y"); ?>
      Sistema ERP Laços & Papéis - Todos os direitos reservados.
    </p>
  </footer>
</body>

</html>