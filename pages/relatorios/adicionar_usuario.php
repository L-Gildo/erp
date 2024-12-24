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
    $nome = $_POST['nome'];  
    $login = $_POST['login'];
    $senha = $_POST['senha'];  
    $confirmarSenha = $_POST['confirmarSenha'];
    $status = $_POST['status'];

    // Validar se as senhas coincidem
    if ($senha !== $confirmarSenha) {
        $mensagemErro = "As senhas não coincidem. Por favor, tente novamente.";
    } else {
        // Hash da senha
        $senha_hash = hash('sha256', $senha);

        // Inserir o usuário no banco de dados
        $sql = "INSERT INTO usuarios (nome, login, senha, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nome, $login, $senha_hash, $status);

        if ($stmt->execute()) {
            $mensagemSucesso = "Usuário adicionado com sucesso!";
        } else {
            $mensagemErro = "Erro ao adicionar o usuário: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Usuário</title>
    <link rel="stylesheet" href="/erp/styles/adicionar_usuario.css">
</head>
<body>
    <div class="form-container">
        <h2>Adicionar Usuário</h2>

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

        <form method="POST" action="adicionar_usuario.php">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="form-group">
                <label for="confirmarSenha">Confirmar Senha:</label>
                <input type="password" id="confirmarSenha" name="confirmarSenha" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Adicionar Usuário</button>
        </form>
    </div>

    <!-- Rodapé -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Sistema ERP Laços & Papel - Todos os direitos reservados.</p>
    </footer>

</body>
</html>



