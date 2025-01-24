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
  // Captura os dados enviados pelo formulário
  $nomeItem = $_POST['itemName'];
  $descricaoItem = $_POST['itemDescription'];
  $codigoItem = $_POST['itemCode'];
  $categoriaItem = $_POST['itemCategory'];
  $marcaItem = $_POST['itemBrand'];
  $modeloItem = $_POST['itemModel'];
  $corItem = $_POST['itemColor'];
  $dimensoesItem = $_POST['itemDimensions'];
  $pesoItem = $_POST['itemWeight'];
  $condicaoItem = $_POST['itemCondition'];
  $localizacaoItem = $_POST['itemLocation'];

  // SQL para inserir os dados na tabela 'equipamentos_moveis'
  $sql = "INSERT INTO equipamentos_moveis 
            (nome_item, descricao_item, codigo_item, categoria_item, marca_item, modelo_item, cor_item, dimensoes_item, peso_item, condicao_item, localizacao_item) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  // Preparar a consulta
  $stmt = $conn->prepare($sql);

  if (!$stmt) {
    $mensagemErro = "Erro na preparação da consulta: " . $conn->error;
  } else {
    // Associar os parâmetros
    $stmt->bind_param(
      "sssssssssss",
      $nomeItem,
      $descricaoItem,
      $codigoItem,
      $categoriaItem,
      $marcaItem,
      $modeloItem,
      $corItem,
      $dimensoesItem,
      $pesoItem,
      $condicaoItem,
      $localizacaoItem
    );

    // Executar a consulta
    if ($stmt->execute()) {
      $mensagemSucesso = "Item cadastrado com sucesso!";
    } else {
      $mensagemErro = "Erro ao cadastrar o item: " . $stmt->error;
    }

    // Fechar a declaração
    $stmt->close();
  }
}

// Fechar a conexão
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro de Equipamentos e Móveis</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      color: #333;
      padding: 20px;
    }

    .form-container {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
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
    <h2>Cadastro de Equipamentos e Móveis</h2>
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

    <form action="#" method="POST">
      <!-- Informações básicas -->
      <div class="form-group">
        <label for="itemName">Nome do Item:</label>
        <input type="text" id="itemName" name="itemName" placeholder="Ex: Mesa de Escritório" required />
      </div>
      <div class="form-group">
        <label for="itemDescription">Descrição:</label>
        <textarea id="itemDescription" name="itemDescription" rows="4" placeholder="Detalhes do item"></textarea>
      </div>

      <!-- Especificações -->
      <div class="form-group-row">
        <div class="form-group">
          <label for="itemCode">Código:</label>
          <input type="text" id="itemCode" name="itemCode" placeholder="Ex: EQ12345" required />
        </div>
        <div class="form-group">
          <label for="itemCategory">Categoria:</label>
          <select id="itemCategory" name="itemCategory" required>
            <option value="">Selecione</option>
            <option value="equipamento">Equipamento</option>
            <option value="movel">Móvel</option>
            <option value="outros">Outros</option>
          </select>
        </div>
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="itemBrand">Marca:</label>
          <input type="text" id="itemBrand" name="itemBrand" placeholder="Ex: HP" />
        </div>
        <div class="form-group">
          <label for="itemModel">Modelo:</label>
          <input type="text" id="itemModel" name="itemModel" placeholder="Ex: ProDesk 600" />
        </div>
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="itemColor">Cor:</label>
          <input type="text" id="itemColor" name="itemColor" placeholder="Ex: Preto" />
        </div>
        <div class="form-group">
          <label for="itemDimensions">Dimensões (LxAxP):</label>
          <input type="text" id="itemDimensions" name="itemDimensions" placeholder="Ex: 120x75x60 cm" />
        </div>
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="itemWeight">Peso (Kg):</label>
          <input type="number" id="itemWeight" name="itemWeight" step="0.01" placeholder="Ex: 15.5" />
        </div>
        <div class="form-group">
          <label for="itemCondition">Condição:</label>
          <select id="itemCondition" name="itemCondition" required>
            <option value="">Selecione</option>
            <option value="novo">Novo</option>
            <option value="novo">Semi Novo</option>
            <option value="usado">Usado</option>
            <option value="novo">Muito Usado</option>
          </select>
        </div>
      </div>
      <!-- Localização -->
      <div class="form-group">
        <label for="itemLocation">Localização:</label>
        <input type="text" id="itemLocation" name="itemLocation" placeholder="Ex: Sala 101" />
      </div>
      <!-- Botões -->
      <div class="form-buttons">
        <button type="submit">Salvar</button>
        <button type="reset">Limpar</button>
      </div>
    </form>
  </div>
  <footer class="footer">
    <p>
      &copy;
      <?php echo date("Y"); ?>
      Sistema ERP Laços & Papéis - Todos os direitos reservados.
    </p>
  </footer>
</body>

</html>