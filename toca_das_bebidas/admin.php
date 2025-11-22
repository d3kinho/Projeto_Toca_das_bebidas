<?php
session_start();
include('verificar_admin.php'); 
include('conexao.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
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
                <h1>Painel Master</h1>
            </div>
            <div class="nav-section nav-list">
                <ul>
                    <li class="nav-item"><a href="index.php" class="nav-link">Voltar ao Site</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Sair</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <div class="admin-header">
                <h2>Gerenciador de Produtos</h2>
                <div class="admin-actions">
                    <a href="admin_logs.php" class="btn-admin btn-roxo">
                        Ver Logs
                    </a>
                    <a href="admin_usuarios.php" class="btn-admin btn-azul">
                        Gerenciar Usuários
                    </a>
                    <a href="admin_produto_novo.php" class="btn-admin btn-verde">
                        Novo Produto
                    </a>
                </div>
            </div>

            <div style="overflow-x: auto;"> <table class="tabela-produtos">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço Base</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, nome, categoria, preco, imagem_url FROM produtos ORDER BY categoria, nome";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0):
                            while($produto = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                                </td>
                                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                <td><?php echo htmlspecialchars($produto['categoria']); ?></td>
                                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                <td class="acoes">
                                    <a href="admin_produto_editar.php?id=<?php echo $produto['id']; ?>" class="btn-admin btn-azul">Editar</a>
                                    <a href="admin_produto_deletar.php?id=<?php echo $produto['id']; ?>" class="btn-admin btn-vermelho" onclick="return confirm('Tem certeza?');">Excluir</a>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Nenhum produto cadastrado.</td>
                            </tr>
                        <?php
                        endif;
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</body>
</html>