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
  $nome = $_POST['name'];
  $cpf = $_POST['cpf'];
  $rg = $_POST['rg'];
  $sexo = $_POST['sex'];
  $dataNascimento = $_POST['dob'];
  $estadoCivil = $_POST['civilStatus'];
  $rua = $_POST['street'];
  $numero = $_POST['number'];
  $bairro = $_POST['neighborhood'];
  $cep = $_POST['cep'];
  $municipio = $_POST['city'];
  $uf = $_POST['state'];
  $cargo = $_POST['role'];
  $remuneracaoFixa = $_POST['fixedSalary'];
  $remuneracaoVariavel = $_POST['variableSalary'];
  $dataAdmissao = $_POST['admissionDate'];
  $dataSaida = $_POST['exitDate'] ?? null;

  // Inserir o colaborador no banco de dados
  $sql = "INSERT INTO colaboradores 
            (nome, cpf, rg, sexo, data_nascimento, estado_civil, rua, numero, bairro, cep, municipio, uf, cargo, remuneracao_fixa, remuneracao_variavel, data_admissao, data_saida) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "sssssssssssssssss",
    $nome,
    $cpf,
    $rg,
    $sexo,
    $dataNascimento,
    $estadoCivil,
    $rua,
    $numero,
    $bairro,
    $cep,
    $municipio,
    $uf,
    $cargo,
    $remuneracaoFixa,
    $remuneracaoVariavel,
    $dataAdmissao,
    $dataSaida
  );

  if ($stmt->execute()) {
    $mensagemSucesso = "Colaborador cadastrado com sucesso!";
  } else {
    $mensagemErro = "Erro ao cadastrar o colaborador: " . $stmt->error;
  }

  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro de Colaborador</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f9;
      color: #333;
    }

    .container {
      max-width: 80%;
      margin: 70px auto;
      padding: 10px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      box-sizing: border-box;
    }

    h1 {
      text-align: center;
      color: #4caf50;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .form-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }

    .form-group label {
      flex: 1 1 auto;
      margin-bottom: 5px;
    }

    .form-group input,
    .form-group select {
      flex: 1 1 auto;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
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

    @media (min-width: 768px) {
      .form-group label {
        flex: 1 1 120px;
        margin-bottom: 0;
      }

      .form-group input,
      .form-group select {
        flex: 2 1 250px;
      }
    }

    .button-group {
      text-align: center;
    }

    .button-group button {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      background-color: #4caf50;
      color: #fff;
      cursor: pointer;
      font-size: 16px;
    }

    .button-group button:hover {
      background-color: #45a049;
    }

    fieldset {
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 10px;
    }

    legend {
      padding: 0 10px;
      font-weight: bold;
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
    <h1>Cadastro de Colaborador</h1>

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

    <form id="collaboratorForm" method="POST" action="adicionar_colaborador.php">
      <fieldset>
        <legend>Dados Pessoais</legend>
        <div class="form-group">
          <label for="name">Nome:</label>
          <input type="text" id="name" name="name" required />

          <label for="cpf">CPF:</label>
          <input type="text" id="cpf" name="cpf" required />

          <label for="rg">RG:</label>
          <input type="text" id="rg" name="rg" />

          <label for="sex">Sexo:</label>
          <select id="sex" name="sex">
            <option value="masculino">Masculino</option>
            <option value="feminino">Feminino</option>
            <option value="outro">Outro</option>
          </select>

          <label for="dob">Data de Nascimento:</label>
          <input type="date" id="dob" name="dob" />

          <label for="civilStatus">Estado Civil:</label>
          <select id="civilStatus" name="civilStatus">
            <option value="solteiro">Solteiro</option>
            <option value="casado">Casado</option>
            <option value="divorciado">Divorciado</option>
            <option value="viúvo">Viúvo</option>
          </select>
        </div>
      </fieldset>

      <fieldset>
        <legend>Endereço</legend>
        <div class="form-group">
          <label for="street">Rua:</label>
          <input type="text" id="street" name="street" />

          <label for="number">Nº:</label>
          <input type="text" id="number" name="number" />

          <label for="neighborhood">Bairro:</label>
          <input type="text" id="neighborhood" name="neighborhood" />

          <label for="cep">CEP:</label>
          <input type="text" id="cep" name="cep" />

          <label for="city">Município:</label>
          <input type="text" id="city" name="city" />

          <label for="state">UF:</label>
          <select id="state" name="state">
            <option value="">Selecione</option>
            <option value="SP">CE</option>
          </select>
        </div>
      </fieldset>

      <fieldset>
        <legend>Cargo/Função</legend>
        <div class="form-group">
          <label for="role">Cargo:</label>
          <select id="role" name="role">
            <option value="aux_admin">Aux. Administrativo</option>
            <option value="caixa">Operador de Caixa</option>
            <option value="vendedor">Vendedor</option>
            <option value="montador">Montador</option>
            <option value="motorista">Motorista</option>
            <option value="motorista">Decorador</option>
            <option value="motorista">Atendente</option>
            <option value="motorista">Gerente</option>
            <option value="motorista">Administrador</option>
          </select>

          <label for="fixedSalary">Remuneração Fixa:</label>
          <input type="number" id="fixedSalary" name="fixedSalary" />

          <label for="variableSalary">Remuneração Variável:</label>
          <input type="number" id="variableSalary" name="variableSalary" />
        </div>
      </fieldset>

      <fieldset>
        <legend>Datas de Contrato</legend>
        <div class="form-group">
          <label for="admissionDate">Data de Admissão:</label>
          <input type="date" id="admissionDate" name="admissionDate" required />

          <label for="exitDate">Data de Saída:</label>
          <input type="date" id="exitDate" name="exitDate" />
        </div>
      </fieldset>

      <div class="button-group">
        <button type="submit" class="btn-submit">Adicionar Usuário</button>
        <button type="reset">Limpar</button>
      </div>
    </form>
  </div>
  <!-- Rodapé -->
  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> Sistema ERP Laços & Papéis - Todos os direitos reservados.</p>
  </footer>

</body>

</html>