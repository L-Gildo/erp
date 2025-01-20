<?php
session_start();
include('db.php'); // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header("Location: /erp/pages/login/login.php");
    exit();
}

// Verifica se há filtro por intervalo de datas e por usuário
$dataInicio = isset($_POST['data_inicio']) ? $_POST['data_inicio'] : null;
$dataFim = isset($_POST['data_fim']) ? $_POST['data_fim'] : null;
$filtroUsuario = isset($_POST['usuario_filtro']) ? $_POST['usuario_filtro'] : null;

// Consulta SQL inicial
$sql = "SELECT u.nome, l.tipo_acao, l.data_hora 
        FROM log_usuarios l
        JOIN usuarios u ON l.usuario_id = u.id";

// Adiciona filtros, se aplicáveis
$parametros = [];
$condicoes = [];

if ($dataInicio && $dataFim) {
    $condicoes[] = "DATE(l.data_hora) BETWEEN ? AND ?";
    $parametros[] = $dataInicio;
    $parametros[] = $dataFim;
} elseif ($dataInicio) {
    $condicoes[] = "DATE(l.data_hora) >= ?";
    $parametros[] = $dataInicio;
} elseif ($dataFim) {
    $condicoes[] = "DATE(l.data_hora) <= ?";
    $parametros[] = $dataFim;
}

if ($filtroUsuario) {
    $condicoes[] = "l.usuario_id = ?";
    $parametros[] = $filtroUsuario;
}

if (!empty($condicoes)) {
    $sql .= " WHERE " . implode(" AND ", $condicoes);
}

// Ordena os resultados
$sql .= " ORDER BY l.data_hora DESC";

// Prepara e executa a consulta
$stmt = $conn->prepare($sql);
if (!empty($parametros)) {
    $stmt->bind_param(str_repeat('s', count($parametros)), ...$parametros);
}
$stmt->execute();
$result = $stmt->get_result();

// Consulta para pegar a lista de usuários
$sqlUsuarios = "SELECT id, nome FROM usuarios";
$resultUsuarios = $conn->query($sqlUsuarios);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Ações dos Usuários</title>
    <link rel="stylesheet" href="/erp/styles/style_visualizar_logs.css" />
</head>

<body>

    <header>
        <h1>Logs de Ações dos Usuários</h1>
    </header>

    <div class="container">
        <h2>Relatório de Acessos</h2>

        <!-- Formulário de Filtro -->
        <form method="post" action="">
            <label for="data_inicio">Data inicial:</label>
            <input type="date" id="data_inicio" name="data_inicio" value="<?php echo htmlspecialchars($dataInicio); ?>">

            <label for="data_fim">Data final:</label>
            <input type="date" id="data_fim" name="data_fim" value="<?php echo htmlspecialchars($dataFim); ?>">

            <label for="usuario_filtro">Filtrar por usuário:</label>
            <select id="usuario_filtro" name="usuario_filtro">
                <option value="">Todos os usuários</option>
                <?php while ($usuario = $resultUsuarios->fetch_assoc()): ?>
                    <option value="<?php echo $usuario['id']; ?>" <?php echo $filtroUsuario == $usuario['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($usuario['nome']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Filtrar</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Data e Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($row['tipo_acao'])); ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($row['data_hora'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Não há registros de login/logout para os filtros selecionados.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Sistema ERP Laços & Papéis. Todos os direitos reservados.</p>
    </div>

</body>

</html>