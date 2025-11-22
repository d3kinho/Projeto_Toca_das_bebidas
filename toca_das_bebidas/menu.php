<?php 
session_start();
include('conexao.php');

// ================================================================
// BUSCAR PRODUTOS DO BANCO
// ================================================================
$produtos_por_categoria = [];

$sql = "
    SELECT 
        p.id AS produto_id, 
        p.nome, 
        p.categoria, 
        p.imagem_url,
        p.preco AS preco_unico, -- Preço para itens sem variação (Whisky, Bebidas)
        v.id AS variacao_id,
        v.descricao AS variacao_descricao,
        v.preco AS variacao_preco
    FROM 
        produtos p
    LEFT JOIN 
        produto_variacoes v ON p.id = v.produto_id
    ORDER BY 
        p.categoria, p.nome, v.preco
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categoria = $row['categoria'];
        $produto_id = $row['produto_id'];

        if (!isset($produtos_por_categoria[$categoria][$produto_id])) {
            $produtos_por_categoria[$categoria][$produto_id] = [
                'id' => $produto_id,
                'nome' => $row['nome'],
                'imagem_url' => $row['imagem_url'],
                'preco_unico' => $row['preco_unico'],
                'variacoes' => []
            ];
        }

        if ($row['variacao_id']) {
            $produtos_por_categoria[$categoria][$produto_id]['variacoes'][] = [
                'id' => $row['variacao_id'],
                'descricao' => $row['variacao_descricao'],
                'preco' => $row['variacao_preco']
            ];
        }
    }
}

$categoria_map = [
    'Cerveja' => 'pizzas',
    'Licor' => 'calzones',
    'Whisky' => 'hamburgueres',
    'Bebida' => 'bebidas'
];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Toca das bebidas - Cardápio</title>

    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    <link rel="stylesheet" href="assets/css/style.css" />
    <script src="assets/js/script.js" defer></script>
    <script src="assets/js/cart.js" defer></script>
    <script src="assets/js/auth.js" defer></script>
    <script src="assets/js/sessao.js" defer></script>
</head>
<body>

    <header>
        <nav class="nav-bar">
        <div class="nav-section">
            <div class="logo">
                <img id="nav-logo" src="assets/img/iconebebida.png" alt="Logo Estação 50" />
                <h1>Toca das Bebidas</h1>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-list">
                <ul>
                    <li class="nav-item"><a href="index.php" class="nav-link">Início</a></li>
                    <li class="nav-item"><a href="menu.php" class="nav-link">Cardápio</a></li>
                </ul>
            </div>
        </div>

        <div class="nav-section">
            <div class="action-buttons">
                <a href="shopcar.php" class="cart-button-link">
                    <div class="cart-icon-wrapper">
                        <img class="nav-img" id="cart-icon-header" src="assets/img/cart(white).png" alt="Carrinho" />
                    </div>
                    <div class="cart-info">
                        <span class="cart-total">R$ 0,00</span>
                        <span class="cart-count">0 itens</span>
                    </div>
                </a>
                <div class="login-button">
                    <button id="openModalBtn">
                        <img class="nav-img" id="login-icon-header" src="assets/img/account(white).png" alt="Login">
                    </button>
                </div>
                <div class="mobile-menu-icon">
                    <button id="mobile-menu-toggle">
                        <img class="icon" id="mobile-menu-icon-img" src="assets/img/img_reserve/menu_white_36dp.png" alt="Abrir Menu" />
                    </button>
                </div>
            </div>
        </div>
    </nav>

        <div class="menu-mobile">
            <ul>
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <img class="nav-img" id="home-mobile-header-mobile" src="assets/img/home(white).png" />
                    </a>
                </li>
                <li class="nav-item">
                    <a href="menu.php" class="nav-link">
                        <img class="nav-img" id="cardapio-menu-header-mobile" src="assets/img/menu(white).png" />
                    </a>
                </li>
            </ul>

            <div class="login-button">
                <button id="openModalBtnMobile">
                    <img class="nav-img" id="login-icon-header-mobile" src="assets/img/account(white).png" alt="Login" />
                </button>
            </div>

            <div class="theme-toggle">
                <button id="theme-toggle-btn-mobile" title="Alternar tema">
                    <img src="assets/img/darkmode.png" alt="Ícone Tema Escuro">
                </button>
            </div>
        </div>

        <div class="sub-nav-bar">
            <ul class="sub-nav-list">
                <?php 
                $first = true;
                foreach ($produtos_por_categoria as $categoria_nome => $produtos):
                    $id_html = $categoria_map[$categoria_nome] ?? strtolower($categoria_nome);
                    $classe_active = $first ? 'active' : '';
                ?>
                    <li><a href="#" class="category-btn <?php echo $classe_active; ?>" data-target="<?php echo $id_html; ?>"><?php echo htmlspecialchars($categoria_nome); ?></a></li>
                <?php 
                    $first = false;
                endforeach; 
                ?>
            </ul>
        </div>
    </header>

    <?php 
    $first_tab = true;
    foreach ($produtos_por_categoria as $categoria_nome => $produtos):
        $id_html = $categoria_map[$categoria_nome] ?? strtolower($categoria_nome);
        $classe_active_tab = $first_tab ? 'active' : '';
    ?>
    
    <div class="menu-container <?php echo $classe_active_tab; ?>" id="<?php echo $id_html; ?>">
        <h2><?php echo htmlspecialchars($categoria_nome); ?></h2>

        <div class="filters">
            <button class="filter-button">Sem gelo</button>
            <button class="filter-button">Adicional de Gelo</button>
        </div>

        <div class="pizza-list">

            <?php 
            foreach ($produtos as $produto):
                $tem_variacoes = !empty($produto['variacoes']);
                $classe_sem_tamanho = !$tem_variacoes ? 'sem-tamanho' : '';
            ?>
            
            <div class="pizza-card <?php echo $classe_sem_tamanho; ?>" 
                 data-image="<?php echo htmlspecialchars($produto['imagem_url']); ?>"
                 data-produto-id="<?php echo $produto['id']; ?>">
                
                <div class="pizza-info">
                    <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                    
                    <?php if ($tem_variacoes): ?>
                        <div class="tags size-selector">
                            <?php foreach ($produto['variacoes'] as $variacao): ?>
                                <button class="size-btn" 
                                        data-variacao-id="<?php echo $variacao['id']; ?>" 
                                        data-size="<?php echo htmlspecialchars($variacao['descricao']); ?>" 
                                        data-price="<?php echo htmlspecialchars($variacao['preco']); ?>">
                                    <?php echo htmlspecialchars($variacao['descricao']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <button class="buy-button">COMPRAR</button>
                    
                    <?php else: ?>
                        <button class="buy-button" 
                                data-price="<?php echo htmlspecialchars($produto['preco_unico']); ?>">
                            COMPRAR
                        </button>
                    <?php endif; ?>

                    <div class="product-error-message"></div>
                </div>
                <img src="<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" />
            </div>

            <?php 
            endforeach;
            ?>

        </div>
    </div>

    <?php 
        $first_tab = false;
    endforeach;
    $conn->close(); 
    ?>

    <footer class="footer">
        <div class="footer-section">
            <div class="font-controls">
                <span>A-</span>
                <button id="decrease-font-btn" title="Diminuir fonte">A</button>
                <button id="increase-font-btn" title="Aumentar fonte">A</button>
                <span>A+</span>
            </div>
        </div>

        <div class="footer-section footer-center">
            <p>&copy; 2025 Toca das Bebidas — Todos os direitos reservados.</p>
        </div>

        <div class="footer-section">
            <div class="theme-toggle">
                <button id="theme-toggle-btn" title="Alternar tema">
                    <img src="assets/img/darkmode.png" alt="Ícone Tema Escuro">
                </button>
            </div>
        </div>
    </footer>

    <div id="loginModal" class="modal-overlay hidden">
        <div class="modal-content">
            <img src="assets/img/img_reserve/close_black_36dp.png" id="close-icon-header" alt="Fechar" class="close-btn">
            <h2>Acessar minha conta</h2>
            <form>
                <label for="identificador">Usuário ou E-mail</label>
                <input type="text" id="identificador" name="identificador" placeholder="Digite seu usuário ou e-mail" required>

                <label for="senha">Senha</label>
                <div class="password-wrapper">
                    <input type="password" id="senha" name="senha" placeholder="Digite a sua senha" required>
                    <button type="button" class="toggle-password" aria-label="Mostrar/ocultar senha">
                        </button>
                </div>

                <div class="forgot-password">
                    <a href="account.php">Não possui uma conta?</a>
                </div>

                <div class="error-message login-error"></div>
                <button type="submit" class="login-submit">Entrar</button>
            </form>
        </div>
    </div>

    <div class="mobile-cart-bar">
        <a href="shopcar.php" class="cart-button-link-mobile">
            <div class="cart-icon-wrapper-mobile">
                <img class="nav-img" id="cart-icon-header" src="assets/img/cart(white).png" alt="Carrinho" />
                <span class="cart-count-mobile-badge">0</span>
            </div>
            <div class="cart-info-mobile">
                <span>Ver carrinho</span>
            </div>
            <div class="cart-total-mobile">
                <span>R$ 0,00</span>
            </div>
        </a>
    </div>

    <div id="top-notification" class="top-notification-bar">
        <span id="top-notification-message"></span>
    </div>

</body>
</html>