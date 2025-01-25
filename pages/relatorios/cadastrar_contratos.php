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
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Captura e sanitização dos dados
  $numeroContrato = trim($_POST['contractNumber'] ?? '');
  $nomeContratante = trim($_POST['contractorName'] ?? '');
  $dataInicio = $_POST['startDate'] ?? null;
  $dataTermino = $_POST['endDate'] ?? null;
  $statusContrato = $_POST['contractStatus'] ?? 'pendente';
  $tipoContrato = $_POST['contractType'] ?? null;
  $valorContrato = trim($_POST['contractValue'] ?? '');
  $descricaoContrato = trim($_POST['contractDescription'] ?? '');
  $observacoes = trim($_POST['additionalNotes'] ?? '');

  // Validação de campos obrigatórios
  if (!$numeroContrato || !$nomeContratante || !$dataInicio || !$dataTermino || !$valorContrato) {
    $mensagemErro = "Todos os campos obrigatórios devem ser preenchidos!";
  } else {
    // Upload de arquivo, se enviado
    $uploadOk = true;
    $arquivoAnexo = null;

    if (!empty($_FILES['fileUpload']['name'])) {
      $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/erp/uploads/"; // Caminho absoluto
      $nomeArquivo = preg_replace('/[^\w.-]/', '_', basename($_FILES['fileUpload']['name'])); // Sanitizar nome do arquivo
      $arquivoAnexo = $targetDir . $nomeArquivo;
      $tipoArquivo = strtolower(pathinfo($arquivoAnexo, PATHINFO_EXTENSION));

      // Verificar tipo de arquivo permitido
      if (!in_array($tipoArquivo, ['pdf', 'png', 'jpg', 'jpeg'])) {
        $mensagemErro = "Somente arquivos PDF, PNG, JPG e JPEG são permitidos.";
        $uploadOk = false;
      }

      // Tentar mover o arquivo para o diretório de upload
      if ($uploadOk && !move_uploaded_file($_FILES['fileUpload']['tmp_name'], $arquivoAnexo)) {
        $mensagemErro = "Erro ao fazer o upload do arquivo.";
        $uploadOk = false;
      }
    }


    // Inserir no banco de dados se não houve erro no upload
    if ($uploadOk && !$mensagemErro) {
      $sql = "INSERT INTO contratos 
              (numero_contrato, nome_contratante, data_inicio, data_termino, status_contrato, tipo_contrato, valor_contrato, descricao_contrato, observacoes, anexo) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

      if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
          "ssssssdsss",
          $numeroContrato,
          $nomeContratante,
          $dataInicio,
          $dataTermino,
          $statusContrato,
          $tipoContrato,
          $valorContrato,
          $descricaoContrato,
          $observacoes,
          $arquivoAnexo
        );

        if ($stmt->execute()) {
          $mensagemSucesso = "Contrato cadastrado com sucesso!";
        } else {
          $mensagemErro = "Erro ao cadastrar o contrato: " . $stmt->error;
        }
        $stmt->close();
      } else {
        $mensagemErro = "Erro na preparação da consulta: " . $conn->error;
      }
    }
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
  <title>Cadastro de Contratos</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    body {
      padding: 20px;
      background-color: #f4f4f9;
    }

    .form-container {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }

    .form-section {
      margin-bottom: 20px;
    }

    .form-section h3 {
      margin-bottom: 10px;
      color: #555;
    }

    .form-group {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }

    .form-group label {
      width: 100%;
      font-size: 14px;
      color: #444;
      margin-bottom: 5px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    .form-group textarea {
      resize: vertical;
    }

    .form-actions {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .form-actions button {
      flex: 1;
      padding: 10px;
      font-size: 16px;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .form-actions button.save {
      background-color: #28a745;
    }

    .form-actions button.cancel {
      background-color: #dc3545;
    }

    @media (min-width: 600px) {
      .form-group label {
        width: calc(50% - 15px);
      }

      .form-group input,
      .form-group select,
      .form-group textarea {
        width: calc(50% - 15px);
      }

      .form-group textarea {
        width: 100%;
      }
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

  <form action="" method="POST" enctype="multipart/form-data">
    <div class="form-container">
      <h2>Cadastro de Contratos</h2>

      <!-- Exibição de mensagens -->
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

      <!-- Informações Gerais -->
      <div class="form-section">
        <h3>Informações Gerais</h3>
        <div class="form-group">
          <label for="contractNumber">Número do Contrato *</label>
          <input type="text" id="contractNumber" name="contractNumber" required />

          <label for="contractorName">Nome do Contratante *</label>
          <input type="text" id="contractorName" name="contractorName" required />

          <label for="startDate">Data de Início *</label>
          <input type="date" id="startDate" name="startDate" required />

          <label for="endDate">Data de Término *</label>
          <input type="date" id="endDate" name="endDate" required />

          <label for="contractStatus">Status do Contrato</label>
          <select id="contractStatus" name="contractStatus">
            <option value="ativo">Ativo</option>
            <option value="pendente">Pendente</option>
            <option value="cancelado">Cancelado</option>
          </select>
        </div>
      </div>

      <!-- Dados do Contrato -->
      <div class="form-section">
        <h3>Dados do Contrato</h3>
        <div class="form-group">
          <label for="contractType">Tipo de Contrato</label>
          <select id="contractType" name="contractType">
            <option value="anual">Anual</option>
            <option value="mensal">Mensal</option>
            <option value="semanal">Semanal</option>
            <option value="diaria">Diária</option>
            <option value="projeto">Por Projeto</option>
            <option value="servico">Por Serviço</option>
            <option value="evento">Por Evento</option>
            <option value="outro">Outro</option>
          </select>

          <label for="contractValue">Valor do Contrato *</label>
          <input type="text" id="contractValue" name="contractValue" required placeholder="R$" />

          <label for="contractDescription">Descrição do Contrato</label>
          <textarea id="contractDescription" name="contractDescription" rows="4"></textarea>
        </div>
      </div>

      <!-- Informações Adicionais -->
      <div class="form-section">
        <h3>Informações Adicionais</h3>
        <div class="form-group">
          <label for="fileUpload">Arquivo Anexo</label>
          <input type="file" id="fileUpload" name="fileUpload" accept=".pdf, .png, .jpg, .jpeg" />

          <label for="additionalNotes">Observações</label>
          <textarea id="additionalNotes" name="additionalNotes" rows="4"></textarea>
        </div>
      </div>

      <!-- Ações do Formulário -->
      <div class="form-actions">
        <button type="submit" class="save">Salvar</button>
        <button type="reset" class="cancel">Cancelar</button>
      </div>
    </div>
  </form>




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