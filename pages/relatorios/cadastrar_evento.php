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

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// Variáveis para mensagens
$mensagemErro = null;
$mensagemSucesso = null;

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Captura os dados do formulário com tratamento para valores nulos
  $dataEvento = isset($_POST['eventDate']) && !empty($_POST['eventDate'])
    ? DateTime::createFromFormat('d/m/Y', $_POST['eventDate'])->format('y-m-d')
    : null;

  $nomeEvento = isset($_POST['eventName']) ? $_POST['eventName'] : null;
  $cliente = isset($_POST['eventClient']) ? $_POST['eventClient'] : null;
  $descricao = isset($_POST['eventDescription']) ? $_POST['eventDescription'] : null;
  $horario = isset($_POST['eventTime']) ? $_POST['eventTime'] : null;
  $local = isset($_POST['eventLocation']) ? $_POST['eventLocation'] : null;

  // Verifica se os campos obrigatórios foram preenchidos
  if ($dataEvento && $nomeEvento && $cliente && $horario) {
    // Query para inserir o evento no banco de dados
    $sql = "INSERT INTO eventos (data_evento, nome_evento, cliente, descricao, horario, local_evento) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Preparar e executar a consulta
    if ($stmt = $conn->prepare($sql)) {
      $stmt->bind_param("ssssss", $dataEvento, $nomeEvento, $cliente, $descricao, $horario, $local);

      if ($stmt->execute()) {
        $mensagemSucesso = "Evento cadastrado com sucesso!";
      } else {
        $mensagemErro = "Erro ao cadastrar o evento: " . $stmt->error;
      }

      $stmt->close();
    } else {
      $mensagemErro = "Erro na preparação da consulta: " . $conn->error;
    }
  } else {
    $mensagemErro = "Todos os campos obrigatórios devem ser preenchidos!";
  }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro de Eventos</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      color: #333;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .calendar-container {
      margin-top: 40px;
      width: 100%;
      max-width: 800px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }

    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 15px;
      background: #4caf50;
      color: #fff;
    }

    .calendar-header button {
      background: none;
      border: none;
      color: #fff;
      font-size: 1.2rem;
      cursor: pointer;
    }

    .calendar-days {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      background: #ddd;
      text-align: center;
      font-weight: bold;
      padding: 5px 0;
    }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      text-align: center;
    }

    .calendar-cell {
      padding: 15px;
      border: 1px solid #ddd;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .calendar-cell:hover {
      background-color: #8ee491;
      color: #fff;
    }

    .calendar-cell.selected {
      background-color: #4caf50;
      color: #fff;
    }

    .event-form {
      padding: 15px;
    }

    .event-form label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .event-form input,
    .event-form textarea,
    .event-form select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }

    .event-form button {
      width: 30%;
      position: relative;
      left: 37%;
      margin-bottom: 15px;
      padding: 10px;
      background-color: #4caf50;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
    }

    .event-form button:hover {
      background-color: #45a049;
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
      .calendar-container {
        margin: 10px;
      }

      .calendar-cell {
        padding: 10px;
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
  <h1>Cadastro de Eventos e Festas</h1>

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


  <div class="calendar-container">
    <div class="calendar-header">
      <button id="prevMonth">&#9664;</button>
      <h2 id="currentMonth">Janeiro 2024</h2>
      <button id="nextMonth">&#9654;</button>
    </div>
    <div class="calendar-days">
      <span>Dom</span><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span>
    </div>
    <div class="calendar-grid" id="calendarGrid"></div>

    <div class="event-form" id="eventForm">
      <h3>Cadastro de Evento</h3>
      <?php if ($mensagemErro): ?>
        <p style="color: red;"><?= $mensagemErro ?></p>
      <?php endif; ?>
      <?php if ($mensagemSucesso): ?>
        <p style="color: green;"><?= $mensagemSucesso ?></p>
      <?php endif; ?>

      <form action="#" method="POST">
        <label for="eventDate">Data do Evento:</label>
        <input type="text" id="eventDate" name="eventDate" placeholder="Data do evento" readonly required />

        <label for="eventName">Nome do Evento:</label>
        <input type="text" id="eventName" name="eventName" placeholder="Digite o nome do evento" required />

        <label for="eventClient">Cliente:</label>
        <input type="text" id="eventClient" name="eventClient" placeholder="Digite o nome do cliente" required />

        <label for="eventDescription">Descrição:</label>
        <textarea id="eventDescription" name="eventDescription" placeholder="Digite os detalhes do evento"
          rows="4"></textarea>

        <label for="eventTime">Horário:</label>
        <input type="time" id="eventTime" name="eventTime" required />

        <label for="eventLocation">Local:</label>
        <input type="text" id="eventLocation" name="eventLocation" placeholder="Digite o local do evento" />

        <button type="submit">Salvar Evento</button>
      </form>
    </div>
  </div>

  <footer class="footer">
    <p>
      &copy;
      <?php echo date("Y"); ?>
      Sistema ERP Laços & Papéis - Todos os direitos reservados.
    </p>
  </footer>

  <script>
    const calendarGrid = document.getElementById("calendarGrid");
    const eventForm = document.getElementById("eventForm");
    const eventDateInput = document.getElementById("eventDate");
    const currentMonth = document.getElementById("currentMonth");

    let date = new Date();

    function renderCalendar() {
      const year = date.getFullYear();
      const month = date.getMonth();

      currentMonth.textContent = date.toLocaleDateString("pt-BR", {
        month: "long",
        year: "numeric",
      });

      const firstDayIndex = new Date(year, month, 1).getDay();
      const lastDay = new Date(year, month + 1, 0).getDate();

      calendarGrid.innerHTML = "";

      for (let i = 0; i < firstDayIndex; i++) {
        const emptyCell = document.createElement("div");
        emptyCell.classList.add("calendar-cell");
        calendarGrid.appendChild(emptyCell);
      }

      for (let day = 1; day <= lastDay; day++) {
        const cell = document.createElement("div");
        cell.classList.add("calendar-cell");
        cell.textContent = day;

        cell.addEventListener("click", () => {
          const selectedDate = new Date(year, month, day).toLocaleDateString(
            "pt-BR",
            {
              day: "2-digit",
              month: "2-digit",
              year: "numeric",
            }
          );

          // Remove "selected" class from other cells
          document
            .querySelectorAll(".calendar-cell.selected")
            .forEach((cell) => cell.classList.remove("selected"));

          // Highlight selected cell
          cell.classList.add("selected");

          // Show the form and update the date
          eventForm.style.display = "block";
          eventDateInput.value = selectedDate;
        });

        calendarGrid.appendChild(cell);
      }
    }

    document.getElementById("prevMonth").addEventListener("click", () => {
      date.setMonth(date.getMonth() - 1);
      renderCalendar();
    });

    document.getElementById("nextMonth").addEventListener("click", () => {
      date.setMonth(date.getMonth() + 1);
      renderCalendar();
    });

    renderCalendar();
  </script>
</body>

</html>