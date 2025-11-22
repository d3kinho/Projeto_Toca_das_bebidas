<?php 
session_start();
include('conexao.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Toca das Bebidas</title>

    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    <link rel="stylesheet" href="assets/css/style.css">
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
                    <img id="nav-logo" src="assets/img/iconebebida.png" alt="Logo">
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
                    
                    <div class="user-menu" style="display: none;">
                        <button id="userMenuBtn" class="user-menu-btn" style="background: none; border: none; cursor: pointer;">
                            <img src="assets/img/account(white).png" class="nav-img" alt="Usuário">
                        </button>
                        <div class="user-dropdown" id="userDropdownContent" style="display: none; position: absolute; background: white; padding: 10px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); right: 6rem; top: 80px; z-index: 1000;">
                            <p id="userNameDisplay" style="margin-bottom: 10px; font-weight: bold; color: #333;"></p>
                            <a href="#" onclick="abrirConfig2FA(); return false;" style="display: block; margin-bottom: 5px; text-decoration: none; color: #007bff;"> Segurança (2FA)</a>
                            <a href="logout.php" style="display: block; text-decoration: none; color: #dc3545;">Sair</a>
                        </div>
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
    </header>

    <main class="container">
        <div class="promo-grid">
            <div class="promo-card wide">
                <a href="menu.php">
                    <img src="assets/img/promoHeiGeneric.png" alt="Promoção Principal">
                </a>
            </div>
            <div class="promo-card">
                <a href="menu.php">
                    <img src="assets/img/cocaPromo.png" alt="Promoção Secundária 1">
                </a>
            </div>
            <div class="promo-card">
                <a href="menu.php">
                    <img src="assets/img/budweiser generico.png" alt="Promoção Secundária 2">
                </a>
            </div>
            <div class="promo-card wide">
                <a href="menu.php">
                    <img src="assets/img/imagemepicadebebida.png" alt="Promoção Banner">
                </a>
            </div>
        </div>
    </main>

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
            <p>&copy; 2025 Toca das Bebidas – Todos os direitos reservados.</p>
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
            <img src="assets/img/img_reserve/close_black_36dp.png" id="close-icon-login" alt="Fechar" class="close-btn">
            <h2>Acessar minha conta</h2>
            <form>
                <label for="identificador">Usuário ou E-mail</label>
                <input type="text" id="identificador" name="identificador" placeholder="Digite seu usuário ou e-mail" required>

                <label for="senha">Senha</label>
                <div class="password-wrapper">
                    <input type="password" name="senha" id="senha" placeholder="Digite a sua senha" required>
                    <button type="button" class="toggle-password" aria-label="Mostrar/ocultar senha">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                <button type="submit" class="login-submit">Entrar</button>
            </form>
        </div>
    </div>

    <div id="config2FAModal" class="modal-overlay hidden">
        <div class="modal-content">
            <img src="assets/img/img_reserve/close_black_36dp.png" 
                 alt="Fechar" 
                 class="close-btn" 
                 onclick="document.getElementById('config2FAModal').classList.add('hidden')">
            <div id="config2FAContent">
                </div>
        </div>
    </div>

    <div id="modal2FA" class="modal-overlay hidden">
        <div class="modal-content">
            <h2>Autenticação de Segurança</h2>
            <p style="text-align: center; margin-bottom: 15px;">Para sua segurança, responda a pergunta abaixo:</p>
            
            <form id="form2FA">
                <div class="pergunta-2fa" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px; text-align: center; border: 1px solid #ddd;">
                    <strong id="pergunta2FA" style="color: #333; font-size: 1.1rem;">Carregando pergunta...</strong>
                </div>
                
                <div class="form-group">
                    <input type="text" 
                           id="resposta2FA" 
                           name="resposta" 
                           placeholder="Sua resposta aqui..." 
                           required 
                           autocomplete="off"
                           style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
                
                <div id="tentativas2FA" style="text-align: center; font-size: 0.9rem; color: #666; margin-top: 10px;">
                    Tentativas restantes: 3
                </div>
                
                <div class="error-message" id="error2FAValidacao"></div>
                
                <button type="submit" class="login-submit">Verificar</button>
            </form>
        </div>
    </div>

    <div id="top-notification" class="top-notification-bar">
        <span id="top-notification-message"></span>
    </div>
    
</body>
</html>