<?php
session_start();
include('verificar_admin.php');
include('conexao.php');

$mensagem = "";
$produto = null;
$variacoes = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $produto_id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $categoria = trim($_POST['categoria']);
    $preco = str_replace(',', '.', trim($_POST['preco']));
    $imagem_url = trim($_POST['imagem_url']);

    if (empty($nome) || empty($categoria)) {
        $mensagem = "<p class='admin-aviso erro'>Erro: Nome e Categoria são obrigatórios.</p>";
    } else if (empty($preco) && empty($_POST['variacao_preco'][0])) {
         $mensagem = "<p class='admin-aviso erro'>Erro: Você deve fornecer um Preço Base ou pelo menos uma Variação.</p>";
    } else {
        try {
            $preco_final = !empty($preco) ? $preco : 0.00;

            $stmt = $conn->prepare("UPDATE produtos SET nome = ?, categoria = ?, preco = ?, imagem_url = ? WHERE id = ?");
            $stmt->bind_param("ssdsi", $nome, $categoria, $preco_final, $imagem_url, $produto_id);
            $stmt->execute();
            $stmt->close();

            $stmt_del_var = $conn->prepare("DELETE FROM produto_variacoes WHERE produto_id = ?");
            $stmt_del_var->bind_param("i", $produto_id);
            $stmt_del_var->execute();
            $stmt_del_var->close();

            if (isset($_POST['variacao_desc']) && isset($_POST['variacao_preco'])) {
                $descricoes = $_POST['variacao_desc'];
                $precos_var = $_POST['variacao_preco'];
                
                $stmt_var = $conn->prepare("INSERT INTO produto_variacoes (produto_id, descricao, preco) VALUES (?, ?, ?)");
                
                foreach ($descricoes as $index => $descricao) {
                    $preco_var_formatado = str_replace(',', '.', $precos_var[$index]);
                    
                    if (!empty($descricao) && !empty($preco_var_formatado)) {
                        $stmt_var->bind_param("isd", $produto_id, $descricao, $preco_var_formatado);
                        $stmt_var->execute();
                    }
                }
                $stmt_var->close();
            }
            
            $mensagem = "<p class='admin-aviso sucesso'>Produto '{$nome}' atualizado com sucesso! <a href='admin.php'>Voltar para a lista</a></p>";
            
        } catch (Exception $e) {
            $mensagem = "<p class='admin-aviso erro'>Erro ao atualizar: " . $e->getMessage() . "</p>";
        }
    }
}

if (isset($_POST['id'])) {
    $id_para_buscar = $_POST['id'];
} else if (isset($_GET['id'])) {
    $id_para_buscar = $_GET['id'];
} else {
    header('Location: admin.php');
    exit;
}

$stmt_prod = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt_prod->bind_param("i", $id_para_buscar);
$stmt_prod->execute();
$produto_result = $stmt_prod->get_result();

if ($produto_result->num_rows === 0) {
    header('Location: admin.php');
    exit;
}
$produto = $produto_result->fetch_assoc();

$stmt_var_load = $conn->prepare("SELECT * FROM produto_variacoes WHERE produto_id = ? ORDER BY id");
$stmt_var_load->bind_param("i", $id_para_buscar);
$stmt_var_load->execute();
$variacoes_result = $stmt_var_load->get_result();
while ($row = $variacoes_result->fetch_assoc()) {
    $variacoes[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
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
                <h1>Editar Produto</h1>
            </div>
            <div class="nav-section nav-list">
                <ul>
                    <li class="nav-item"><a href="admin.php" class="nav-link">Voltar ao Painel</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Sair</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            
            <?php echo $mensagem; ?>

            <form action="admin_produto_editar.php" method="POST" class="admin-form">
                
                <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                
                <div class="form-group">
                    <label for="nome">Nome do Produto</label>
                    <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($produto['nome']); ?>">
                </div>

                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Selecione</option>
                        <option value="Cerveja" <?php if($produto['categoria'] == 'Cerveja') echo 'selected'; ?>>Cerveja</option>
                        <option value="Licor" <?php if($produto['categoria'] == 'Licor') echo 'selected'; ?>>Licor</option>
                        <option value="Whisky" <?php if($produto['categoria'] == 'Whisky') echo 'selected'; ?>>Whisky</option>
                        <option value="Bebida" <?php if($produto['categoria'] == 'Bebida') echo 'selected'; ?>>Bebida</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="preco">Preço Base (para itens sem variação)</label>
                    <input type="text" id="preco" name="preco" placeholder="Ex: 143,00" value="<?php echo number_format($produto['preco'], 2, ',', '.'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="imagem_url">URL da Imagem</label>
                    <input type="text" id="imagem_url" name="imagem_url" placeholder="Ex: assets/img/Brejas/heineken.jpg" value="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
                </div>

                <div id="variacoes-container">
                    <h3>Variações</h3>
                    <p class="admin-form-ajuda">
                        <br>
                        Se este produto tiver tamanhos/preços diferentes, adicione-os aqui. Se for preço único (como Whisky), deixe em branco.
                    </p>
                    <div id="lista-variacoes">
                        <?php foreach ($variacoes as $var): ?>
                            <div class="variacao-item">
                                <input type="text" name="variacao_desc[]" placeholder="Descrição" required value="<?php echo htmlspecialchars($var['descricao']); ?>">
                                <input type="text" name="variacao_preco[]" placeholder="Preço" required value="<?php echo number_format($var['preco'], 2, ',', '.'); ?>">
                                <button type="button" class="btn-remover-var" onclick="removerVariacao(this)">Remover</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="btn-add-var">+ Adicionar Variação</button>
                </div>

                <div class="form-actions">
                    <a href="admin.php" class="btn-admin btn-cinza">Cancelar</a>
                    <button type="submit" class="btn-admin btn-verde">Atualizar Produto</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('btn-add-var').addEventListener('click', function() {
            const container = document.getElementById('lista-variacoes');
            const novoItem = document.createElement('div');
            novoItem.classList.add('variacao-item');
            
            novoItem.innerHTML = `
                <input type="text" name="variacao_desc[]" placeholder="Descrição (Ex: 1 unidade)" required>
                <input type="text" name="variacao_preco[]" placeholder="Preço (Ex: 6,00)" required>
                <button type="button" class="btn-remover-var" onclick="removerVariacao(this)">Remover</button>
            `;
            
            container.appendChild(novoItem);
        });

        function removerVariacao(botao) {
            botao.parentElement.remove();
        }
    </script>
</body>
</html>