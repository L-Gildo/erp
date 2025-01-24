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
  // Captura dos dados do formulário
  $nomeFornecedor = $_POST['nome'];
  $cnpj = $_POST['cnpj'];
  $email = $_POST['email'];
  $telefone = $_POST['telefone'];
  $endereco = $_POST['endereco'];
  $cidade = $_POST['cidade'];
  $uf = $_POST['uf'];
  $cep = $_POST['cep'];
  $descricao = $_POST['descricao'] ?? null;

  // Inserir o fornecedor no banco de dados
  $sql = "INSERT INTO fornecedores 
            (nome, cnpj, email, telefone, endereco, cidade, uf, cep, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

  // Preparar a consulta
  $stmt = $conn->prepare($sql);

  // Associar os parâmetros
  $stmt->bind_param(
    "sssssssss",
    $nomeFornecedor,
    $cnpj,
    $email,
    $telefone,
    $endereco,
    $cidade,
    $uf,
    $cep,
    $descricao
  );

  // Executar a consulta
  if ($stmt->execute()) {
    $mensagemSucesso = "Fornecedor cadastrado com sucesso!";
  } else {
    $mensagemErro = "Erro ao cadastrar o fornecedor: " . $stmt->error;
  }

  // Fechar a declaração
  $stmt->close();
}

// Fechar a conexão com o banco de dados
$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro de Fornecedor</title>
  <style>
    /* Reset básico */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Arial", sans-serif;
      background-color: #f4f4f9;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .container {
      width: 100%;
      max-width: 900px;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      margin: 20px;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 30px;
    }

    form {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .input-group {
      flex: 1 1 45%;
      /* Responsividade ajustável */
      display: flex;
      flex-direction: column;
    }

    .input-group label {
      font-weight: bold;
      font-size: 16px;
      color: #333;
      margin-bottom: 8px;
    }

    .input-group input,
    .input-group select,
    .input-group textarea {
      padding: 12px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      transition: border-color 0.3s ease;
    }

    .input-group input:focus,
    .input-group select:focus,
    .input-group textarea:focus {
      border-color: #007bff;
      outline: none;
    }

    .input-group textarea {
      resize: vertical;
      min-height: 120px;
    }

    .button-group {
      width: 100%;
      display: flex;
      justify-content: center;
      gap: 15px;
    }

    .button-group button {
      padding: 12px 20px;
      background-color: #4caf50;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .button-group button:hover {
      background-color: #3d9241;
    }

    .button-group button[type="reset"] {
      background-color: #4caf50;
    }

    .button-group button[type="reset"]:hover {
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

    @media (max-width: 768px) {
      .input-group {
        flex: 1 1 100%;
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
  <div class="container">
    <h2>Cadastro de Fornecedor</h2>
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

    <form action="cadastrar_fornecedor.php" method="POST">
      <!-- Dados Básicos -->
      <div class="input-group">
        <label for="nome">Nome do Fornecedor:</label>
        <input type="text" id="nome" name="nome" required />
      </div>
      <div class="input-group">
        <label for="cnpj">CNPJ:</label>
        <input type="text" id="cnpj" name="cnpj" required />
      </div>
      <div class="input-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />
      </div>
      <div class="input-group">
        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" required />
      </div>

      <!-- Endereço -->
      <div class="input-group">
        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco" required />
      </div>
      <div class="input-group">
        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" required />
      </div>
      <div class="input-group">
        <label for="uf">UF:</label>
        <select id="uf" name="uf" required>
          <option value="">Selecione</option>
          <option value="AC">Acre (AC)</option>
          <option value="AL">Alagoas (AL)</option>
          <option value="AP">Amapá (AP)</option>
          <option value="AM">Amazonas (AM)</option>
          <option value="BA">Bahia (BA)</option>
          <option value="CE">Ceará (CE)</option>
          <option value="DF">Distrito Federal (DF)</option>
          <option value="ES">Espírito Santo (ES)</option>
          <option value="GO">Goiás (GO)</option>
          <option value="MA">Maranhão (MA)</option>
          <option value="MT">Mato Grosso (MT)</option>
          <option value="MS">Mato Grosso do Sul (MS)</option>
          <option value="MG">Minas Gerais (MG)</option>
          <option value="PA">Pará (PA)</option>
          <option value="PB">Paraíba (PB)</option>
          <option value="PR">Paraná (PR)</option>
          <option value="PE">Pernambuco (PE)</option>
          <option value="PI">Piauí (PI)</option>
          <option value="RJ">Rio de Janeiro (RJ)</option>
          <option value="RN">Rio Grande do Norte (RN)</option>
          <option value="RS">Rio Grande do Sul (RS)</option>
          <option value="RO">Rondônia (RO)</option>
          <option value="RR">Roraima (RR)</option>
          <option value="SC">Santa Catarina (SC)</option>
          <option value="SP">São Paulo (SP)</option>
          <option value="SE">Sergipe (SE)</option>
          <option value="TO">Tocantins (TO)</option>
        </select>
      </div>
      <div class="input-group">
        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" required />
      </div>

      <!-- Descrição -->
      <div class="input-group" style="flex: 1 1 100%">
        <label for="descricao">Observações:</label>
        <textarea id="descricao" name="descricao" rows="4" required></textarea>
      </div>

      <!-- Botões -->
      <div class="button-group">
        <button type="submit">Cadastrar</button>
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