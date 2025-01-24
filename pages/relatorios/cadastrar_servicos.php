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
  // Captura dos dados do formulário com tratamento de possíveis valores nulos
  $nomeServico = isset($_POST['serviceName']) ? $_POST['serviceName'] : null;
  $descricao = isset($_POST['serviceDescription']) ? $_POST['serviceDescription'] : null;
  $categoria = isset($_POST['serviceCategory']) ? $_POST['serviceCategory'] : null;
  $codigo = isset($_POST['serviceCode']) ? $_POST['serviceCode'] : null;
  $preco = isset($_POST['servicePrice']) ? $_POST['servicePrice'] : null;
  $duracao = isset($_POST['serviceDuration']) ? $_POST['serviceDuration'] : null;
  $prestador = isset($_POST['serviceProvider']) ? $_POST['serviceProvider'] : null;
  $disponibilidade = isset($_POST['serviceAvailability']) ? $_POST['serviceAvailability'] : null;

  // Verifica se os campos obrigatórios foram preenchidos
  if ($nomeServico && $descricao && $categoria && $codigo && $preco) {
    // Inserir o serviço no banco de dados
    $sql = "INSERT INTO servicos 
              (nome_servico, descricao, categoria, codigo, preco, duracao, prestador, disponibilidade) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar a consulta
    if ($stmt = $conn->prepare($sql)) {
      // Associar os parâmetros
      $stmt->bind_param(
        "ssssdsss",
        $nomeServico,
        $descricao,
        $categoria,
        $codigo,
        $preco,
        $duracao,
        $prestador,
        $disponibilidade
      );

      // Executar a consulta
      if ($stmt->execute()) {
        $mensagemSucesso = "Serviço cadastrado com sucesso!";
      } else {
        $mensagemErro = "Erro ao cadastrar o serviço: " . $stmt->error;
      }

      // Fechar a declaração
      $stmt->close();
    } else {
      $mensagemErro = "Erro na preparação da consulta: " . $conn->error;
    }
  } else {
    $mensagemErro = "Todos os campos obrigatórios devem ser preenchidos!";
  }
}

// Fechar a conexão com o banco de dados
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro de Serviço</title>
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
    <h2>Cadastro de Serviço</h2>
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
        <label for="serviceName">Nome do Serviço:</label>
        <input type="text" id="serviceName" name="serviceName" placeholder="Ex: Consultoria de TI" required />
      </div>
      <div class="form-group">
        <label for="serviceDescription">Descrição:</label>
        <textarea id="serviceDescription" name="serviceDescription" rows="4"
          placeholder="Detalhes sobre o serviço"></textarea>
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="serviceCategory">Categoria:</label>
          <select id="serviceCategory" name="serviceCategory" required>
            <option value="">Selecione</option>
            <option value="consultoria">Consultoria</option>
            <option value="manutencao">Manutenção</option>
            <option value="treinamento">Treinamento</option>
            <option value="instalacao">Instalação</option>
            <option value="entrega">Entrega</option>
            <option value="outros">Outros</option>
          </select>
        </div>
        <div class="form-group">
          <label for="serviceCode">Código:</label>
          <input type="text" id="serviceCode" name="serviceCode" placeholder="Ex: SRV123" required />
        </div>
      </div>
      <!-- Detalhes do serviço -->
      <div class="form-group-row">
        <div class="form-group">
          <label for="servicePrice">Preço:</label>
          <input type="number" id="servicePrice" name="servicePrice" step="0.01" placeholder="Ex: 250.00" required />
        </div>
        <div class="form-group">
          <label for="serviceDuration">Duração (horas):</label>
          <input type="number" id="serviceDuration" name="serviceDuration" placeholder="Ex: 2" />
        </div>
      </div>
      <div class="form-group-row">
        <div class="form-group">
          <label for="serviceProvider">Prestador:</label>
          <input type="text" id="serviceProvider" name="serviceProvider" placeholder="Ex: John Doe" />
        </div>
        <div class="form-group">
          <label for="serviceAvailability">Disponibilidade:</label>
          <select id="serviceAvailability" name="serviceAvailability">
            <option value="">Selecione</option>
            <option value="diurno">Diurno</option>
            <option value="noturno">Noturno</option>
            <option value="24horas">24 Horas</option>
          </select>
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
      Sistema ERP Laços e Papéis - Todos os direitos reservados.
    </p>
  </footer>
</body>

</html>