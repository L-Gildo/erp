<?php
session_start();
include('db.php'); // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
      // Redireciona para a página de login se o usuário não estiver logado
      header("Location: /erp/pages/login/login.php");
      exit();
}

// Verifica se ocorreu algum erro na conexão
if ($conn->connect_error) {
      die("Falha na conexão: " . $conn->connect_error);
}

// ID do usuário logado (você deve configurar isso com base na autenticação do sistema)
$usuario_id = $_SESSION['usuario_logado'];

// Verifica se o filtro de data foi enviado
$data_filtro = isset($_POST['data_filtro']) ? $_POST['data_filtro'] : '';

// Consulta para obter o nome do usuário logado
$sql_nome = "SELECT nome FROM usuarios WHERE id = ?";
$stmt_nome = $conn->prepare($sql_nome);
$stmt_nome->bind_param("i", $usuario_id);
$stmt_nome->execute();
$result_nome = $stmt_nome->get_result();
$usuario_nome = $result_nome->fetch_assoc()['nome'];

// Consulta para obter os logs do usuário logado (com filtro de data, se houver)
if ($data_filtro) {
      $sql = "SELECT tipo_acao, data_hora FROM log_usuarios WHERE usuario_id = ? AND DATE(data_hora) = ? ORDER BY data_hora DESC";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("is", $usuario_id, $data_filtro);
} else {
      $sql = "SELECT tipo_acao, data_hora FROM log_usuarios WHERE usuario_id = ? ORDER BY data_hora DESC";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $usuario_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Registros de Entrada e Saída</title>
      <style>
            body {
                  font-family: Arial, sans-serif;
                  margin: auto;
                  padding: 0;
                  background-color: #f9f9f9;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  flex-direction: column;
            }

            .container {
                  max-width: 800px;
                  margin: 0 auto;
                  padding: 20px;
                  background: #ffffff;
                  border-radius: 8px;
                  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                  width: 100%;
            }

            .header {
                  text-align: center;
                  margin-bottom: 20px;
            }

            h2 {
                  font-size: 2rem;
                  /* Ajuste relativo para responsividade */
            }

            p {
                  font-size: 1rem;
            }

            form {
                  margin-bottom: 20px;
                  display: flex;
                  justify-content: space-between;
                  flex-wrap: wrap;
                  gap: 10px;
            }

            form label,
            form input,
            form button {
                  font-size: 1rem;
                  padding: 8px;
                  margin: 5px;
            }

            input[type="date"] {
                  width: 100%;
                  max-width: 150px;
            }

            button {
                  background-color: #4CAF50;
                  color: white;
                  border: none;
                  cursor: pointer;
                  font-size: 1rem;
            }

            button:hover {
                  background-color: #45a049;
            }

            /* Tabela */
            table {
                  width: 100%;
                  border-collapse: collapse;
                  margin-top: 20px;
                  font-size: 1rem;
            }

            th,
            td {
                  padding: 10px;
                  text-align: left;
                  border: 1px solid #ddd;
            }

            th {
                  background-color: #f4f4f4;
            }

            tbody tr:nth-child(odd) {
                  background-color: #f9f9f9;
            }

            tbody tr:nth-child(even) {
                  background-color: #ffffff;
            }

            @media (max-width: 768px) {
                  table {
                        font-size: 0.9rem;
                  }

                  th,
                  td {
                        padding: 8px;
                  }

                  input[type="date"] {
                        max-width: 120px;
                  }

                  button {
                        font-size: 0.9rem;
                        padding: 7px;
                  }

                  h2 {
                        font-size: 1.6rem;
                  }
            }

            @media (max-width: 480px) {
                  body {
                        padding: 10px;
                        flex-direction: column;
                  }

                  .container {
                        padding: 0px;
                  }

                  table {
                        font-size: 0.85rem;
                  }

                  th,
                  td {
                        padding: 0px;
                  }

                  input[type="date"],
                  button {
                        font-size: 0.9rem;
                  }

                  h2 {
                        font-size: 1.4rem;
                  }

                  /* Responsividade para a tabela em telas menores */
                  table,
                  thead,
                  tbody,
                  th,
                  td,
                  tr {
                        display: block;
                        width: 98%;
                  }

                  thead tr {
                        display: none;
                  }

                  tr {
                        margin-bottom: 15px;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                  }

                  td {
                        text-align: right;
                        padding-left: 50%;
                        position: relative;
                  }

                  td:before {
                        content: attr(data-label);
                        position: absolute;
                        left: 10px;
                        width: 45%;
                        text-align: left;
                        font-weight: bold;
                        white-space: nowrap;
                  }

                  form {
                        flex-direction: column;
                        align-items: flex-start;
                  }
            }

            @media (max-width: 1024px) {
                  h2 {
                        font-size: 1.8rem;
                  }

                  table {
                        font-size: 1.1rem;
                  }

                  th,
                  td {
                        padding: 0px;
                  }
            }

            .footer {
                  width: 90%;
                  height: 20px;
                  text-align: center;
                  font-size: 8px;
                  color: #333;
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
            <div class="header">
                  <h2>Bem-vindo(a), <?php echo htmlspecialchars($usuario_nome); ?>!</h2>
                  <p>Confira abaixo seus registros de entrada e saída:</p>
            </div>

            <!-- Formulário para filtrar a data -->
            <form method="POST" action="">
                  <label for="data_filtro">Filtrar por Data:</label>
                  <input type="date" id="data_filtro" name="data_filtro" value="<?php echo $data_filtro; ?>">
                  <button type="submit">Filtrar</button>
            </form>

            <table>
                  <thead>
                        <tr>
                              <th>Nome do Usuário</th>
                              <th>Tipo de Ação</th>
                              <th>Data e Hora</th>
                        </tr>
                  </thead>
                  <tbody>
                        <?php if ($result->num_rows > 0): ?>
                              <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                          <td data-label="Nome do Usuário"><?php echo htmlspecialchars($usuario_nome); ?></td>
                                          <td data-label="Tipo de Ação"><?php echo htmlspecialchars($row['tipo_acao']); ?></td>
                                          <td data-label="Data e Hora"><?php
                                          // Formatar a data e hora para dd/mm/aaaa hh:mm:ss
                                          $data_hora = new DateTime($row['data_hora']);
                                          echo $data_hora->format('d/m/Y H:i:s');
                                          ?></td>
                                    </tr>
                              <?php endwhile; ?>
                        <?php else: ?>
                              <tr>
                                    <td colspan="3">Nenhum registro encontrado.</td>
                              </tr>
                        <?php endif; ?>
                  </tbody>
            </table>
      </div>
      <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> Sistema ERP Laços & Papéis. Todos os direitos reservados.</p>
      </div>
</body>

</html>
<?php
$stmt_nome->close();
$stmt->close();
$conn->close();
?>