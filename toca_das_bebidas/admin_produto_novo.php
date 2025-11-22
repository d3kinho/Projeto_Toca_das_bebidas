<?php
session_start();
include('verificar_admin.php'); 
include('conexao.php');

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nome = trim($_POST['nome']);
    $categoria = trim($_POST['categoria']);
    $preco_base_str = str_replace(',', '.', trim($_POST['preco']));
    $imagem_url = trim($_POST['imagem_url']);
    
    if (empty($nome) || empty($categoria)) {
        $mensagem = "<p class='admin-aviso erro'>Erro: Nome e Categoria são obrigatórios.</p>";
    } else if (empty($preco_base_str) && (!isset($_POST['variacao_preco']) || empty($_POST['variacao_preco'][0]))) {
         $mensagem = "<p class='admin-aviso erro'>Erro: Você deve fornecer um Preço Base ou pelo menos uma Variação de preço.</p>";
    } else {
        
        $conn->begin_transaction();
        $sucesso_total = true;
        $erro_msg = "";

        $preco_final = !empty($preco_base_str) ? $preco_base_str : 0.00;

        $stmt = $conn->prepare("INSERT INTO produtos (nome, categoria, preco, imagem_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $nome, $categoria, $preco_final, $imagem_url);
        
        if (!$stmt->execute()) {
            $sucesso_total = false;
            $erro_msg = $stmt->error;
        }
        $produto_id = $stmt->insert_id; 
        $stmt->close();

        if ($sucesso_total && isset($_POST['variacao_desc']) && isset($_POST['variacao_preco'])) {
            $descricoes = $_POST['variacao_desc'];
            $precos_var = $_POST['variacao_preco'];
            
            $stmt_var = $conn->prepare("INSERT INTO produto_variacoes (produto_id, descricao, preco) VALUES (?, ?, ?)");
            
            foreach ($descricoes as $index => $descricao) {
                $preco_var_formatado = str_replace(',', '.', $precos_var[$index]);
                
                if (!empty($descricao) && !empty($preco_var_formatado)) {
                    if (!is_numeric($preco_var_formatado)) {
                        $sucesso_total = false;
                        $erro_msg = "Preço da variação inválido: '{$precos_var[$index]}'. Use apenas números (ex: 6,00).";
                        break;
                    }

                    $stmt_var->bind_param("isd", $produto_id, $descricao, $preco_var_formatado);
                    
                    if (!$stmt_var->execute()) {
                        $sucesso_total = false;
                        $erro_msg = $stmt_var->error;
                        break;
                    }
                }
            }
            $stmt_var->close();
        }

        if ($sucesso_total) {
            $conn->commit();
            $mensagem = "<p class='admin-aviso sucesso'>Produto '{$nome}' cadastrado com sucesso! <a href='admin.php'>Voltar para a lista</a></p>";
        } else {
            $conn->rollback();
            $mensagem = "<p class='admin-aviso erro'>Erro ao cadastrar: {$erro_msg}</p>";
        }
        
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Novo Produto</title>
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
                <h1>Adicionar Produto</h1>
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

            <form action="admin_produto_novo.php" method="POST" class="admin-form">
                
                <div class="form-group">
                    <label for="nome">Nome do Produto</label>
                    <input type="text" id="nome" name="nome" required>
                </div>

                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="Cerveja">Cerveja</option>
                        <option value="Licor">Licor</option>
                        <option value="Whisky">Whisky</option>
                        <option value="Bebida">Bebida</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="preco">Preço Base (para itens sem variação)</label>
                    <input type="text" id="preco" name="preco" placeholder="Ex: 143,00 (para Whisky, Bebidas)">
                </div>
                
                <div class="form-group">
                    <label for="imagem_url">URL da Imagem</label>
                    <input type="text" id="imagem_url" name="imagem_url" placeholder="Ex: assets/img/Brejas/heineken.jpg">
                </div>

                <div id="variacoes-container">
                    <h3>Variações (para Cervejas, Licores)</h3>
                    <p class="admin-form-ajuda">
                        <br></br>
                        Se este produto tiver tamanhos/preços diferentes, adicione-os aqui. Se for preço único (como Whisky), deixe em branco.
                    </p>
                    <div id="lista-variacoes">
                    </div>
                    <button type="button" id="btn-add-var">+ Adicionar Variação</button>
                </div>

                <div class="form-actions">
                    <a href="admin.php" class="btn-admin btn-cinza">Cancelar</a>
                    <button type="submit" class="btn-admin btn-verde">Salvar Produto</button>
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