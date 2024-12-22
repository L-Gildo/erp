<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    // Redireciona para a página de login
    header("Location: /erp/pages/login/login.html");
    exit();
}

// Verifica se o usuário deseja fazer logout
if (isset($_GET['logout'])) {
    // Destruir todas as variáveis de sessão
    session_unset();
    
    // Destruir a sessão
    session_destroy();
    
    // Redireciona para a página de login
    header("Location: /erp/pages/login/login.html");
    exit();
}
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
  </body>
</html>
