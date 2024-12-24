<?php
session_start();
include('db.php'); // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
// Redireciona para a página de login se o usuário não estiver logado
header("Location: /erp/pages/login/login.php");
exit();
}

// Consulta para buscar os logs de login e logout
$sql = "SELECT u.nome, l.tipo_acao, l.data_hora 
        FROM log_usuarios l
        JOIN usuarios u ON l.usuario_id = u.id
        ORDER BY l.data_hora DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Ações dos Usuários</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        
        header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            background-color: #f4f4f9;
            color: #333;
        }

        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Logs de Ações dos Usuários</h1>
</header>

<div class="container">
    <h2>Relatório de Acessos</h2>
    
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
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo ucfirst($row['tipo_acao']); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($row['data_hora'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Não há registros de login/logout.</p>
    <?php endif; ?>
</div>

<div class="footer">
    <p>&copy; <?php echo date('Y'); ?> Sistema ERP Laços & Papel. Todos os direitos reservados.</p>
</div>

</body>
</html>
