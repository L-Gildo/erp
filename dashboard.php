<?php
session_start();

// Definir o fuso horário correto
date_default_timezone_set('America/Sao_Paulo');

// Conexão com o banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sistema_erp";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    // Redireciona para a página de login
    header("Location: /erp/pages/login/login.php");
    exit();
}

// Verifica se o usuário deseja fazer logout
if (isset($_GET['logout'])) {
    // Antes de destruir a sessão, registra o logout no banco de dados
    $usuario_id = $_SESSION['usuario_logado'];  // ID do usuário que está deslogando
    $tipo_acao = 'logout';

    // Registra o logout no banco de dados
    $sql = "INSERT INTO log_usuarios (usuario_id, tipo_acao) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $usuario_id, $tipo_acao);
    $stmt->execute();
    
    // Destruir todas as variáveis de sessão
    session_unset();
    
    // Destruir a sessão
    session_destroy();

    // Redireciona para a página de login
    header("Location: /erp/pages/login/login.php");
    exit();
}

$nome_usuario = $_SESSION['usuario_nome'];
$data_hora_login = $_SESSION['data_hora_login'];
?>



<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ERP Laços & Papel</title>
    <link rel="stylesheet" href="styles.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />
  </head>
  <body>

<!-- Balão de boas-vindas -->
<div id="welcome-balloon" class="welcome-balloon">
        <p>Bem-vindo(a), <?php echo $nome_usuario; ?>!</p>
        <p>Data e hora do último login: <?php echo $data_hora_login; ?></p>
        <button id="close-balloon" class="close-button">OK</button>
    </div>
    <div class="container">
      <!-- Menu Lateral -->
      <aside class="sidebar collapsed" id="sidebar">
        <button class="toggle-menu" id="menuToggle">
          <span></span>
          <span></span>
          <span></span>
        </button>
        <div class="menu-content">
          <h2>Menu</h2>
          <nav>
            <ul>
              <li>
                <a href="#cadastro"><i class="fa fa-user"></i> Cadastro</a>
              </li>
              <li>
                <a href="#consulta"><i class="fa fa-search"></i> Consulta</a>
              </li>
              <li>
                <a href="#vendas"><i class="fa fa-shopping-cart"></i> Vendas</a>
              </li>
              <li>
                <a href="#relatorios"
                  ><i class="fa fa-file-alt"></i> Relatórios</a
                >
              </li>
              <li>
                <a href="#eventos"><i class="fa fa-calendar"></i> Eventos</a>
              </li>
              <li>
                <a href="#sistema"><i class="fa fa-cogs"></i> Sistema</a>
              </li>
            </ul>
          </nav>
          <div class="logout-container">
            <a href="dashboard.php?logout=true" id="logoutButton">
              <img
                src="images/TelaInicial/sair.png"
                alt="Sair"
                class="logout-icon"
              />
            </a>
          </div>

        </div>
      </aside>

      <!-- Conteúdo Principal -->
      <main class="main-content">
        <header>
          <h1>Sistema Gerencial - Laços & Papel</h1>
        </header>
        <section id="boas-vindas">
          <h2>Bem-vindo ao ERP da Empresa Laços & Papel!</h2>
          <p>Última atualização: 19 de dezembro de 2024</p>
          <p>Confira suas opções abaixo para gerenciar seus processos.</p>
          <div class="quick-links">
            <a href="#cadastro">Cadastro</a>
            <a href="#vendas">Vendas</a>
          </div>
        </section>
      </main>
    </div>
    <script src="script.js"></script>
    <div id="notification-panel" class="notification-panel">
      <p>
        <strong>Atenção!</strong> O sistema foi atualizado para a versão 2.1.
      </p>
      <button id="close-notification">X</button>
    </div>

    <script>
        // Fechar balão de boas-vindas ao clicar no botão OK
        document.getElementById("close-balloon").addEventListener("click", function() {
            document.getElementById("welcome-balloon").style.display = "none";
            // Mover a informação para o canto inferior direito
            var balloonInfo = document.createElement("div");
            balloonInfo.classList.add("welcome-info");
            balloonInfo.innerHTML = "Bem-vindo(a), <?php echo $nome_usuario; ?>! Último login: <?php echo $data_hora_login; ?>";
            document.body.appendChild(balloonInfo);
        });
    </script>

  </body>
</html>
