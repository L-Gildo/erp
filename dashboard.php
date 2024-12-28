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

// Lógica para gerar os cards restritos
// Incluir Usuários
function gerarCardUsuarios($nome_usuario)
{
  if ($nome_usuario === "Leonardo Gildo" || $nome_usuario === "Diélifa") {
    return '<a href="/erp/pages/relatorios/adicionar_usuario.php" class="card">
                  <h3>Usuários</h3>
                  <p>Gerencie os usuários do sistema.</p>
              </a>';
  } else {
    return '<div class="card disabled">
                  <h3>Usuários</h3>
                  <p>Gerencie os usuários do sistema.</p>
              </div>';
  }
}

// Incluir Colaboradores
function gerarCardColaboradores($nome_usuario)
{
  if ($nome_usuario === "Leonardo Gildo" || $nome_usuario === "Diélifa") {
    return '<a href="/erp/pages/relatorios/adicionar_colaborador.php" class="card">
                  <h3>Colaboradores</h3>
                  <p>Gerencie seus colaboradores.</p>
              </a>';
  } else {
    return '<div class="card disabled">
                  <h3>Colaboradores</h3>
                  <p>Gerencie seus colaboradores.</p>
              </div>';
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ERP Laços & Papéis</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="styles/modal.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body>

  <!-- Balão de boas-vindas -->
  <div id="welcome-balloon" class="welcome-balloon" style="display: none;">
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
              <a href="#cadastro"><i class="fa fa-user"></i> Cadastros</a>
            </li>
            <li>
              <a href="#consulta"><i class="fa fa-search"></i> Consultas</a>
            </li>
            <li>
              <a href="#vendas"><i class="fa fa-shopping-cart"></i> Vendas</a>
            </li>
            <li>
              <a href="#relatorios"><i class="fa fa-file-alt"></i> Relatórios</a>
            </li>
            <li>
              <a href="#eventos"><i class="fa fa-calendar"></i> Eventos</a>
            </li>
            <li>
              <a href="#sistema"><i class="fa fa-cogs"></i> Sistema</a>
            </li>
          </ul>
        </nav>
      </div>
      <div class="logout-container">
        <a href="dashboard.php?logout=true" id="logoutButton">
          <img src="images/TelaInicial/sair.png" alt="Sair" class="logout-icon" />
        </a>
      </div>
    </aside>

    <!-- Conteúdo Principal -->
    <main class="main-content">
      <header>
        <h1>Sistema Gerencial - Laços & Papéis</h1>
      </header>
      <section id="boas-vindas">
        <h2>Bem-vindo ao ERP da Empresa Laços & Papéis!</h2>
        <p class="atualizacao">Última atualização: 19 de dezembro de 2024</p>
        <p class="aviso">Confira as opções de <b>acesso rápido</b> abaixo para gerenciar seus processos.</p>
        <div class="quick-links">
          <a href="#cadastro">Nova Venda</a>
          <a href="#vendas">Meu Ponto</a>
        </div>
      </section>
    </main>
  </div>

  <!-- Scripts -->
  <script src="script.js"></script>
  <script src="scripts/scriptCadastro.js"></script>
  <!-- Scripts -->

  <!-- Quadro Flutuante com os Subitens do Menu Cadastro - CSS modal -->
  <div id="cadastroModal" class="modal" style="display: none;">
    <div class="modal-content">
      <span id="closeModal" class="closeCadastro">&times;</span>
      <h2>Menu Cadastros</h2>
      <div id="access-balloon" class="floating-balloon" style="display: none;">
        <p>Acesso somente ao gestor</p>
      </div>
      <div class="card-container">
        <?php echo gerarCardUsuarios($nome_usuario); ?>

        <?php echo gerarCardColaboradores($nome_usuario); ?>

        <a href="/erp/pages/relatorios/adicionar_cliente.php" class="card">
          <h3>Clientes</h3>
          <p>Dados de clientes registrados.</p>
        </a>

        <a href="produtos.html" class="card">
          <h3>Produtos</h3>
          <p>Gerencie os produtos cadastrados.</p>
        </a>

        <a href="vendedores.html" class="card">
          <h3>Vendedores</h3>
          <p>Cadastre os vendedores da sua empresa.</p>
        </a>

        <a href="estoque.html" class="card">
          <h3>Estoque</h3>
          <p>Controle o estoque de produtos.</p>
        </a>

        <a href="fornecedores.html" class="card">
          <h3>Fornecedores</h3>
          <p>Gerencie seus fornecedores.</p>
        </a>

        <a href="equipamentos.html" class="card">
          <h3>Equipamentos</h3>
          <p>Cadastre e gerencie os equipamentos.</p>
        </a>

        <a href="servicos.html" class="card">
          <h3>Serviços</h3>
          <p>Gerencie os serviços oferecidos.</p>
        </a>

        <a href="festa.html" class="card">
          <h3>Festa</h3>
          <p>Organize os eventos e festas.</p>
        </a>

        <a href="contratos.html" class="card">
          <h3>Contratos</h3>
          <p>Gerencie suas aplicações e contratos.</p>
        </a>

        <a href="compras.html" class="card">
          <h3>Compras</h3>
          <p>Controle seu estoque e gerencie o material.</p>
        </a>

        <a href="/erp/pages/relatorios/meu_ponto.php" class="card">
          <h3>Meu ponto</h3>
          <p>Veja aqui os dias e horários logados.</p>
        </a>
      </div>
    </div>
  </div>


  <script>
    // Função para calcular a diferença em segundos
    function calcularDiferencaSegundos(dataHoraLogin) {
      const dataHoraAtual = new Date();
      const dataHoraLoginObj = new Date(dataHoraLogin);
      return (dataHoraAtual - dataHoraLoginObj) / 1000; // Diferença em segundos
    }

    // Exibir ou não o balão de boas-vindas baseado no tempo de login
    window.onload = function () {
      const dataHoraLogin = "<?php echo $data_hora_login; ?>"; // Recebe a data de login via PHP
      const diferencaEmSegundos = calcularDiferencaSegundos(dataHoraLogin);

      if (diferencaEmSegundos <= 20) {
        // Se a diferença for menor ou igual a 20 segundos, exibe o balão
        document.getElementById("welcome-balloon").style.display = "block";
      } else {
        // Caso contrário, exibe a mensagem no canto inferior
        const balloonInfo = document.createElement("div");
        balloonInfo.classList.add("welcome-info");
        balloonInfo.innerHTML = "Bem-vindo(a), <?php echo $nome_usuario; ?>! Último login: <?php echo $data_hora_login; ?>";
        document.body.appendChild(balloonInfo);
      }
    };

    // Fechar balão de boas-vindas ao clicar no botão OK
    document.getElementById("close-balloon").addEventListener("click", function () {
      document.getElementById("welcome-balloon").style.display = "none";
      // Mover a informação para o canto inferior direito
      var balloonInfo = document.createElement("div");
      balloonInfo.classList.add("welcome-info");
      balloonInfo.innerHTML = "Bem-vindo(a), <?php echo $nome_usuario; ?>! Último login: <?php echo $data_hora_login; ?>";
      document.body.appendChild(balloonInfo);
    });

    document.querySelectorAll(".card.disabled").forEach(card => {
      card.addEventListener("click", () => {
        const balloon = document.getElementById("access-balloon");
        balloon.style.display = "block";

        setTimeout(() => {
          balloon.style.display = "none";
        }, 3000); // Balão desaparece após 3 segundos
      });
    });

  </script>

</body>

</html>