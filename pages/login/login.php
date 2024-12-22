<?php
session_start();
$host = "localhost"; // Endereço do servidor de banco de dados
$user = "root"; // Usuário do MySQL
$password = ""; // Senha do MySQL
$dbname = "sistema_erp"; // Nome do banco de dados

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login']; // Usuário (email ou nome de usuário)
    $senha = $_POST['senha']; // Senha do usuário

    // Consulta para verificar o usuário no banco de dados
    $sql = "SELECT * FROM usuarios WHERE login = ? AND status = 'ativo'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o usuário existe
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $senha_armazenada = $usuario['senha'];

        // Comparar a senha digitada com a senha armazenada
        if (hash('sha256', $senha) === $senha_armazenada) {
            // Login bem-sucedido
            $_SESSION['usuario_logado'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header("Location: /erp/dashboard.php"); // Redirecionar para o dashboard
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "Usuário não encontrado ou inativo!";
    }
}

$conn->close();
?>


