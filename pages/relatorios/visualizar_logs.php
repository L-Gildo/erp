<?php
session_start();
include('db.php'); // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    // Redireciona para a página de login se o usuário não estiver logado
    header("Location: /erp/pages/login/login.php");
    exit();
}

// Verifica se há filtro por data e por usuário
$filtroData = isset($_POST['data_filtro']) ? $_POST['data_filtro'] : null;
$filtroUsuario = isset($_POST['usuario_filtro']) ? $_POST['usuario_filtro'] : null;

// Consulta SQL inicial
$sql = "SELECT u.nome, l.tipo_acao, l.data_hora 
        FROM log_usuarios l
        JOIN usuarios u ON l.usuario_id = u.id";

// Adiciona filtros, se aplicáveis
$parametros = [];
if ($filtroData) {
    $sql .= " WHERE DATE(l.data_hora) = ?";
    $parametros[] = $filtroData;
}
if ($filtroUsuario) {
    if (count($parametros) > 0) {
        $sql .= " AND l.usuario_id = ?";
    } else {
        $sql .= " WHERE l.usuario_id = ?";
    }
    $parametros[] = $filtroUsuario;
}

// Ordena os resultados
$sql .= " ORDER BY l.data_hora DESC";

// Prepara e executa a consulta
$stmt = $conn->prepare($sql);
if (!empty($parametros)) {
    // Bind dos parâmetros (data e/ou usuário)
    $stmt->bind_param(str_repeat('s', count($parametros)), ...$parametros);
}
$stmt->execute();
$result = $stmt->get_result();

// Consulta para pegar a lista de usuários
$sqlUsuarios = "SELECT id, nome FROM usuarios";
$resultUsuarios = $conn->query($sqlUsuarios);

// Função para exportar PDF
if (isset($_POST['exportar_pdf'])) {
    // Instancia o objeto FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Título
    $pdf->Cell(200, 10, 'Relatório de Logs de Ações dos Usuários', 0, 1, 'C');
    $pdf->Ln(10);

    // Cabeçalho da tabela
    $pdf->Cell(50, 10, 'Usuário', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Ação', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Data e Hora', 1, 1, 'C');

    // Dados da tabela
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(50, 10, htmlspecialchars($row['nome']), 1, 0, 'C');
        $pdf->Cell(50, 10, ucfirst(htmlspecialchars($row['tipo_acao'])), 1, 0, 'C');
        $pdf->Cell(60, 10, date('d/m/Y H:i:s', strtotime($row['data_hora'])), 1, 1, 'C');
    }

    // Gera o PDF
    $pdf->Output('D', 'relatorio_logs.pdf');
    exit();
}
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
            <label for="data_filtro">Filtrar por data:</label>
            <input type="date" id="data_filtro" name="data_filtro" value="<?php echo htmlspecialchars($filtroData); ?>">

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
        <p>&copy; <?php echo date('Y'); ?> Sistema ERP Laços & Papeis. Todos os direitos reservados.</p>
    </div>

</body>

</html>