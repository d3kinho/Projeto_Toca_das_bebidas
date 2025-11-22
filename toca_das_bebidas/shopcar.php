<?php 
session_start();
include('conexao.php');

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Toca das bebidas - Carrinho</title>

    <script>
        (function () {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    <link rel="stylesheet" href="assets/css/cart.css" />
    <link rel="stylesheet" href="assets/css/style.css" />

    <script src="assets/js/script.js" defer></script>
    <script src="assets/js/cart.js" defer></script>
    <script src="assets/js/auth.js" defer></script>
    <script src="assets/js/sessao.js" defer></script>
</head>

<body class="pagina-carrinho">

    <!-- header -->
    <header>
        <nav class="nav-bar">
            <div class="nav-section">
                <div class="logo">
                    <img id="nav-logo" src="assets/img/iconebebida.png" alt="Logo Toca das bebidas" />
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

        <!-- mobile -->
        <div class="menu-mobile">
            <ul>
                <li class="nav-item"><a href="index.php" class="nav-link">
                    <img class="nav-img" id="home-mobile-header-mobile" src="assets/img/home(white).png" />
                </a></li>
                <li class="nav-item"><a href="menu.php" class="nav-link">
                    <img class="nav-img" id="cardapio-menu-header-mobile" src="assets/img/menu(white).png" />
                </a></li>
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
    </header>

    <!-- conte´´udo - Carrinho -->
    <main class="container-carrinho">
        <div class="coluna-pedido">
            <div class="cabecalho-pedido">
                <h2>Confirme seu pedido</h2>
                <a href="#" class="limpar-carrinho" onclick="clearCart()">Limpar carrinho</a>
            </div>
            <div class="lista-itens-carrinho" id="cartItemsContainer"></div>
        </div>

        <div class="coluna-checkout">
            <div class="box-checkout">
                <section class="secao-checkout">
                    <h3>Retirada desejada</h3>
                    <div class="opcao-entrega">
                        <label class="radio-container">
                            <input type="radio" name="deliveryType" checked>
                            <span class="radio-custom"></span>
                            Buscar na loja
                        </label>
                        <div class="loja-info">
                            <p>Estação 50, Guaratiba</p>
                            <p class="texto-cinza">CEP: 12345678, Rua Aricuri, 23 Guaratiba, Rio de Janeiro - RJ</p>
                        </div>
                    </div>
                </section>

                <section class="secao-checkout">
                    <h3>Horário da retirada</h3>
                    <label class="radio-container">
                        <input type="radio" name="deliveryTime" checked>
                        <span class="radio-custom"></span>
                        Retirar agora
                    </label>
                    <label class="radio-container">
                        <input type="radio" name="deliveryTime">
                        <span class="radio-custom"></span>
                        Agendar retirada
                    </label>
                </section>

                <section class="secao-resumo">
                    <div class="linha-resumo">
                        <span>Subtotal</span>
                        <span id="subtotal">R$ 0,00</span>
                    </div>
                    <div class="linha-total">
                        <span>Total</span>
                        <span id="total">R$ 0,00</span>
                    </div>
                </section>

                <div class="botoes-acao">
                    <a href="menu.php" class="btn-adicionar-itens">Adicionar mais itens</a>
                    <button class="btn-pagamento" onclick="goToPayment()">Ir para pagamento</button>
                </div>
            </div>
        </div>
    </main>

    <!-- rodapé -->
    <footer class="footer">
        <div class="footer-section"></div>

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

    <!-- janela de Login -->
    <div id="loginModal" class="modal-overlay hidden">
        <div class="modal-content">
            <img src="assets/img/img_reserve/close_black_36dp.png" id="close-icon-header" alt="Fechar" class="close-btn">
            <h2>Acessar minha conta</h2>
            <form>
                <label for="identificador">Usuário ou E-mail</label>
                <input type="text" id="identificador" name="identificador" placeholder="Digite seu usuário ou e-mail" required>

                <label for="senha">Senha</label>
                <div class="password-wrapper">
                    <input type="password" id="senha" placeholder="Digite a sua senha" required>
                    <button type="button" class="toggle-password" aria-label="Mostrar/ocultar senha">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-eye-off-icon">
                            <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
                            <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
                            <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
                            <path d="m2 2 20 20"/>
                        </svg>
                    </button>
                </div>

                <div class="forgot-password">
                    <a href="account.php">Não possui uma conta?</a>
                </div>

                <div class="error-message login-error"></div>
                <button type="submit" class="login-submit">Login</button>
            </form>
        </div>
    </div>

    <div id="top-notification" class="top-notification-bar">
        <span id="top-notification-message"></span>
    </div>

</body>

</html>
