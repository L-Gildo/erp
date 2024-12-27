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
      $nomeCliente = $_POST['cliente'];
      $cpf = $_POST['cpf'];
      $rg = $_POST['rg'];
      $telefone1 = $_POST['phone'];
      $telefone2 = $_POST['phone2'];
      $rua = $_POST['street'];
      $numero = $_POST['number'];
      $bairro = $_POST['neighborhood'];
      $cep = $_POST['cep'];
      $municipio = $_POST['city'];
      $uf = $_POST['state'];
      $dataCadastro = $_POST['cadastroDate'];
      $observacoes = $_POST['observations'] ?? null;

      // Inserir o cliente no banco de dados
      $sql = "INSERT INTO clientes 
            (cliente, cpf, rg, telefone1, telefone2, rua, numero, bairro, cep, municipio, estado, data_cadastro, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

      // Preparar a consulta
      $stmt = $conn->prepare($sql);

      // Associar os parâmetros
      $stmt->bind_param(
            "sssssssssssss",
            $nomeCliente,
            $cpf,
            $rg,
            $telefone1,
            $telefone2,
            $rua,
            $numero,
            $bairro,
            $cep,
            $municipio,
            $uf,
            $dataCadastro,
            $observacoes
      );

      // Executar a consulta
      if ($stmt->execute()) {
            $mensagemSucesso = "Cliente cadastrado com sucesso!";
      } else {
            $mensagemErro = "Erro ao cadastrar o cliente: " . $stmt->error;
      }

      // Fechar a declaração
      $stmt->close();
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Cadastro de Cliente</title>
      <style>
            body {
                  font-family: Arial, sans-serif;
                  margin: 0;
                  padding: 0;
                  background-color: #f4f4f9;
                  color: #333;
            }

            .container {
                  max-width: 73%;
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
                  gap: 10px;
            }

            .form-group label {
                  margin-bottom: 5px;
            }

            .form-group input,
            .form-group select {
                  width: 100%;
                  max-width: 300px;
                  padding: 8px;
                  border: 1px solid #ccc;
                  border-radius: 4px;
                  box-sizing: border-box;
                  margin: 5px 0;
            }

            .form-group textarea {
                  height: 100px;
                  width: 98%;
                  padding: 2px;
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
                        flex: 1 1 100px;
                        margin-bottom: 0;
                  }

                  .form-group input,
                  .form-group select {
                        flex: 2 1 100px;
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
                  margin: 5px;
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
            <h1>Cadastro de Cliente - Laços e Papéis</h1>

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

            <form id="collaboratorForm" method="POST" action="adicionar_cliente.php">
                  <fieldset>
                        <legend>Dados Pessoais</legend>
                        <div class="form-group">
                              <label for="client">Cliente:</label>
                              <input type="text" id="client" name="cliente" required />

                              <label for="cpf">CPF:</label>
                              <input type="text" id="cpf" name="cpf" required />

                              <label for="rg">RG:</label>
                              <input type="text" id="rg" name="rg" />

                              <label for="phone">Telefone1:</label>
                              <input type="tel" id="phone" name="phone" required />

                              <label for="phone2">Telefone2:</label>
                              <input type="tel" id="phone2" name="phone2" required />
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
                        <legend>Data de cadastro</legend>
                        <div class="form-group">
                              <label for="cadastroDate">Data de cadastro:</label>
                              <input type="date" id="cadastroDate" name="cadastroDate" required />
                        </div>
                  </fieldset>

                  <fieldset class="form-section">
                        <legend>Outras Informações</legend>
                        <div class="form-group">
                              <label for="observations">Observações:</label>
                              <textarea id="observations" name="observations" rows="6"></textarea>
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