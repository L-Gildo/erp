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

$error_message = ""; // Variável para armazenar mensagens de erro

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    // Consulta no banco
    $sql = "SELECT * FROM usuarios WHERE login = ? AND status = 'ativo'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $senha_armazenada = $usuario['senha'];

        if (hash('sha256', $senha) === $senha_armazenada) {
            $_SESSION['usuario_logado'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['data_hora_login'] = date("d/m/Y H:i:s"); // Salva data e hora do login

            // Registrar login no banco de dados
            $usuario_id = $usuario['id'];
            $tipo_acao = 'login';
            $sql = "INSERT INTO log_usuarios (usuario_id, tipo_acao) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $usuario_id, $tipo_acao);
            $stmt->execute();

            header("Location: /erp/dashboard.php");
            exit();
        } else {
            $error_message = "Senha incorreta!";
        }
    } else {
        $error_message = "Usuário não encontrado ou inativo!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ERP Empresa</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <div class="login-box">
            <h2>Laços & Papeis</h2>
            <h2>Login</h2>
            <form method="post" action="">
                <div class="input-container">
                    <label for="login">Usuário:</label>
                    <input type="text" id="login" name="login" placeholder="Digite seu usuário" required />
                </div>
                <div class="input-container">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required />
                </div>
                <button type="submit" class="login-button">Entrar</button>
            </form>

            <!-- Exibir mensagem de erro -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Rodapé -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Sistema ERP Laços & Papéis - Todos os direitos reservados.</p>
    </footer>
</body>

</html>