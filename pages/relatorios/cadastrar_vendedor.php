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
  $nome = $_POST['sellerName'];
  $email = $_POST['sellerEmail'];
  $cpf = $_POST['sellerCPF'];
  $telefone = $_POST['sellerPhone'];
  $dataInicio = $_POST['sellerStartDate'];
  $status = $_POST['sellerStatus'];
  $endereco = $_POST['sellerAddress'];

  // Inserir o vendedor no banco de dados
  $sql = "INSERT INTO vendedores 
            (nome, email, cpf, telefone, data_inicio, status, endereco) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "sssssss",
    $nome,
    $email,
    $cpf,
    $telefone,
    $dataInicio,
    $status,
    $endereco
  );

  if ($stmt->execute()) {
    $mensagemSucesso = "Vendedor cadastrado com sucesso!";
  } else {
    $mensagemErro = "Erro ao cadastrar o vendedor: " . $stmt->error;
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
  <title>Cadastro de Vendedor</title>
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
    <img src="/erp/images/home-button.png" title="Voltar ao Dashboard Laços e papéis" alt="voltar" />
  </a>
  <div class="form-container">
    <h2>Cadastro de Vendedor</h2>
    <!-- Exibir mensagens de sucesso ou erro -->
    <?php if ($mensagemErro): ?>
      <p style="color: red; text-align: center;"><?php echo $mensagemErro; ?></p>
    <?php endif; ?>
    <?php if ($mensagemSucesso): ?>
      <p style="color: green; text-align: center;"><?php echo $mensagemSucesso; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
      <!-- Informações pessoais -->
      <div class="form-group">
        <label for="sellerName">Nome do Vendedor:</label>
        <input type="text" id="sellerName" name="sellerName" placeholder="Ex: João Silva" required />
      </div>
      <div class="form-group">
        <label for="sellerEmail">E-mail:</label>
        <input type="email" id="sellerEmail" name="sellerEmail" placeholder="Ex: joao.silva@email.com" required />
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="sellerCPF">CPF:</label>
          <input type="text" id="sellerCPF" name="sellerCPF" placeholder="Ex: 123.456.789-00" required />
        </div>
        <div class="form-group">
          <label for="sellerPhone">Telefone:</label>
          <input type="text" id="sellerPhone" name="sellerPhone" placeholder="Ex: (11) 98765-4321" required />
        </div>
      </div>
      <!-- Informações adicionais -->
      <div class="form-group-row">
        <div class="form-group">
          <label for="sellerStartDate">Data de Início:</label>
          <input type="date" id="sellerStartDate" name="sellerStartDate" required />
        </div>
        <div class="form-group">
          <label for="sellerStatus">Status:</label>
          <select id="sellerStatus" name="sellerStatus" required>
            <option value="">Selecione</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="sellerAddress">Endereço:</label>
        <textarea id="sellerAddress" name="sellerAddress" rows="4"
          placeholder="Ex: Rua ABC, 123, Bairro XYZ, São Paulo, SP"></textarea>
      </div>
      <!-- Botões -->
      <div class="form-buttons">
        <button type="submit">Salvar</button>
        <button type="reset">Limpar</button>
      </div>
    </form>
  </div>
  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> Sistema ERP Laços & Papéis - Todos os direitos reservados.</p>
  </footer>
</body>

</html>