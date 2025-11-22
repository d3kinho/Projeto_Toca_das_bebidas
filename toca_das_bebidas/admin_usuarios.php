<?php
session_start();
include('verificar_admin.php');
include('conexao.php');

$mensagem_feedback = "";

if (isset($_SESSION['feedback_admin'])) {
    $mensagem_feedback = $_SESSION['feedback_admin'];
    unset($_SESSION['feedback_admin']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
</head>
<body>

    <header>
        <nav class="nav-bar">
            <div class="nav-section logo">
                <h1>Gestão de Usuários</h1>
            </div>
            <div class="nav-section nav-list">
                <ul>
                    <li class="nav-item"><a href="admin.php" class="nav-link">Gestão de Produtos</a></li>
                    <li class="nav-item"><a href="index.php" class="nav-link">Voltar ao Site</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Sair</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="admin-container" style="max-width: 1200px;"> <div class="admin-header">
                <h2>Todos os Usuários Registrados</h2>
                </div>

            <?php echo $mensagem_feedback; ?>

            <table class="tabela-produtos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Login</th>
                        <th>CPF</th>
                        <th>Celular</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT id, nome, email, login, cpf, celular, perfil FROM usuarios ORDER BY nome";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0):
                        while($usuario = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['login']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['celular']); ?></td>
                            <td>
                                <span style="font-weight: bold; <?php echo $usuario['perfil'] == 'master' ? 'color: #dc3545;' : ''; ?>">
                                    <?php echo htmlspecialchars($usuario['perfil']); ?>
                                </span>
                            </td>
                            <td class="acoes">
                                <a href="admin_usuario_editar.php?id=<?php echo $usuario['id']; ?>" class="btn-admin btn-azul">Editar</a>
                                <a href="admin_usuario_deletar.php?id=<?php echo $usuario['id']; ?>" class="btn-admin btn-vermelho" onclick="return confirm('Tem CERTEZA que deseja deletar este usuário? Esta ação não pode ser desfeita.');">Excluir</a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php
                    endif;
                    $conn->close();
                    ?>
                </tbody>
            </table>

        </div>
    </main>
</body>
</html>