// ============================================
// script.js - FUNÇÕES GERAIS DO SITE
// ============================================

function showTopNotification(message, type = 'info') {
    const notificationBar = document.getElementById('top-notification');
    const notificationMessage = document.getElementById('top-notification-message');
    if (!notificationBar || !notificationMessage) return;
    
    notificationMessage.textContent = message;
    notificationBar.className = `top-notification-bar top-notification-bar--${type}`;
    notificationBar.classList.add('show');
    
    setTimeout(() => {
        notificationBar.classList.remove('show');
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    
    // ===== MENU MOBILE =====
    const menuToggleButton = document.getElementById('mobile-menu-toggle');
    const toggleMenu = () => {
        const menuMobile = document.querySelector('.menu-mobile');
        const menuIcon = document.getElementById('mobile-menu-icon-img');
        const iconHamburger = 'assets/img/img_reserve/menu_white_36dp.png';
        const iconClose = 'assets/img/img_reserve/close_white_36dp.png';

        menuMobile.classList.toggle('open');

        if (menuMobile.classList.contains('open')) {
            menuIcon.src = iconClose;
            menuIcon.alt = "Fechar Menu";
        } else {
            menuIcon.src = iconHamburger;
            menuIcon.alt = "Abrir Menu";
        }
    };

    if (menuToggleButton) {
        menuToggleButton.addEventListener('click', toggleMenu);
    }

    // ===== TEMA CLARO/ESCURO =====
    const themeToggleFooterBtn = document.getElementById('theme-toggle-btn');
    const themeToggleMobileBtn = document.getElementById('theme-toggle-btn-mobile');

    function updateIconsForTheme() {
        const isDarkMode = document.documentElement.classList.contains('dark-mode');
        const loginIcon = document.getElementById('login-icon-header');
        const cartIcon = document.getElementById('cart-icon-header');
        const cardapioIcon = document.getElementById('cardapio-menu-header-mobile');
        const homeIcon = document.getElementById('home-mobile-header-mobile');
        const loginmobIcon = document.getElementById('login-icon-header-mobile');
        const closeIcon = document.getElementById('close-icon-header');
        const xloginIcon = document.getElementById('close-icon-login');

        const iconPaths = {
            login: { dark: 'assets/img/account(white).png', light: 'assets/img/account.png' },
            cart: { dark: 'assets/img/cart(white).png', light: 'assets/img/cart.png' },
            cardapio: { dark: 'assets/img/ticket(white).png', light: 'assets/img/ticket.png' },
            home: { dark: 'assets/img/home(white).png', light: 'assets/img/home.png' },
            loginmob: { dark: 'assets/img/account(white).png', light: 'assets/img/account.png' },
            close: { dark: 'assets/img/img_reserve/close_white_36dp.png', light: 'assets/img/img_reserve/close_black_36dp.png' },
            xlogin: {dark: 'assets/img/img_reserve/close_white_36dp.png', light:'assets/img/img_reserve/close_black_36dp.png' }
        };

        if (loginIcon) loginIcon.src = isDarkMode ? iconPaths.login.dark : iconPaths.login.light;
        if (cartIcon) cartIcon.src = isDarkMode ? iconPaths.cart.dark : iconPaths.cart.light;
        if (cardapioIcon) cardapioIcon.src = isDarkMode ? iconPaths.cardapio.dark : iconPaths.cardapio.light;
        if (homeIcon) homeIcon.src = isDarkMode ? iconPaths.home.dark : iconPaths.home.light;
        if (loginmobIcon) loginmobIcon.src = isDarkMode ? iconPaths.loginmob.dark : iconPaths.loginmob.light;
        if (closeIcon) closeIcon.src = isDarkMode ? iconPaths.close.dark : iconPaths.close.light;
        if (xloginIcon) xloginIcon.src = isDarkMode ? iconPaths.xlogin.dark : iconPaths.xlogin.light;
    }

    const toggleTheme = () => {
        document.documentElement.classList.toggle('dark-mode');
        if (document.documentElement.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
        } else {
            localStorage.removeItem('theme');
        }
        updateIconsForTheme();
    };

    if (themeToggleFooterBtn) themeToggleFooterBtn.addEventListener('click', toggleTheme);
    if (themeToggleMobileBtn) themeToggleMobileBtn.addEventListener('click', toggleTheme);
    updateIconsForTheme();

    // ===== MODAL DE LOGIN (abrir/fechar) =====
    const modal = document.getElementById('loginModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const openModalBtnMobile = document.getElementById('openModalBtnMobile');
    const closeBtn = document.querySelector('.close-btn');
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.getElementById('senha');

    // Abrir modal
    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            if (modal) modal.classList.remove('hidden');
        });
    }
    
    if (openModalBtnMobile) {
        openModalBtnMobile.addEventListener('click', () => {
            if (modal) modal.classList.remove('hidden');
        });
    }

    // Fechar modal
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            if (modal) modal.classList.add('hidden');
        });
    }

    if (modal) {
        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    }

    // Mostrar/ocultar senha
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
        });
    }

    // ===== CATEGORIAS DO MENU (cardápio) =====
    const categoryButtons = document.querySelectorAll(".category-btn");
    const menuContainers = document.querySelectorAll(".menu-container");

    if (categoryButtons.length > 0 && menuContainers.length > 0) {
        categoryButtons[0].classList.add("active");
        menuContainers[0].classList.add("active");

        categoryButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                const targetId = button.getAttribute("data-target");

                categoryButtons.forEach(btn => btn.classList.remove("active"));
                button.classList.add("active");

                menuContainers.forEach(container => container.classList.remove("active"));
                const targetContainer = document.getElementById(targetId);
                if (targetContainer) targetContainer.classList.add("active");
            });
        });
    }

    // ===== CONTROLE DE TAMANHO DE FONTE =====
    const increaseFontBtn = document.getElementById('increase-font-btn');
    const decreaseFontBtn = document.getElementById('decrease-font-btn');
    const htmlElement = document.documentElement;

    const fontLevels = {
        'default': '16px',
        'medium': '18px',
        'large': '20px'
    };

    const setFontSize = (level) => {
        htmlElement.style.fontSize = fontLevels[level];
        localStorage.setItem('fontSize', level);
    };

    const changeFontSize = (direction) => {
        const currentSize = getComputedStyle(htmlElement).fontSize;
        const levels = Object.keys(fontLevels);

        let currentLevelIndex = levels.findIndex(level => fontLevels[level] === currentSize);
        if (currentLevelIndex === -1) currentLevelIndex = 0;

        if (direction === 'increase' && currentLevelIndex < levels.length - 1) {
            currentLevelIndex++;
        } else if (direction === 'decrease' && currentLevelIndex > 0) {
            currentLevelIndex--;
        }

        setFontSize(levels[currentLevelIndex]);
    };

    const savedFontSize = localStorage.getItem('fontSize');
    if (savedFontSize && fontLevels[savedFontSize]) {
        setFontSize(savedFontSize);
    }

    if (increaseFontBtn) increaseFontBtn.addEventListener('click', () => changeFontSize('increase'));
    if (decreaseFontBtn) decreaseFontBtn.addEventListener('click', () => changeFontSize('decrease'));

    // ===== FILTROS DE PRODUTOS =====
    const filterButtons = document.querySelectorAll('.filters .filter-button');
    const productCards = document.querySelectorAll('.pizza-card');

    if (filterButtons.length > 0 && productCards.length > 0) {
        productCards.forEach(card => {
            card.querySelectorAll('.size-btn').forEach(button => {
                button.dataset.basePrice = button.dataset.price;
            });
        });

        filterButtons[0].classList.add('active');

        filterButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                e.currentTarget.classList.add('active');

                const surcharge = e.currentTarget.textContent.trim() === 'Premium' ? 10 : 0;
                updateAllProductPrices(surcharge);
            });
        });

        function updateAllProductPrices(surcharge) {
            productCards.forEach(card => {
                const sizeButtons = card.querySelectorAll('.size-btn');
                const buyButton = card.querySelector('.buy-button');

                sizeButtons.forEach(sizeBtn => {
                    const basePrice = parseFloat(sizeBtn.dataset.basePrice);
                    const newPrice = basePrice + surcharge;
                    sizeBtn.dataset.price = newPrice.toFixed(2);
                });

                const activeSizeButton = card.querySelector('.size-btn.active');
                if (activeSizeButton) {
                    const newPrice = parseFloat(activeSizeButton.dataset.price);
                    const formattedPrice = `R$ ${newPrice.toFixed(2).replace('.', ',')}`;
                    if (buyButton) {
                        buyButton.textContent = formattedPrice;
                        buyButton.dataset.priceText = formattedPrice;
                    }
                }
            });
        }
    }
});

async function renderCartItems() {
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    const subtotalSpan = document.getElementById('subtotal');
    const totalSpan = document.getElementById('total');

    if (!cartItemsContainer) return; // Só roda na página do carrinho

    try {
        const response = await fetch('carrinho_listar.php');
        const data = await response.json();

        cartItemsContainer.innerHTML = ''; // Limpa o carrinho

        if (!data.loggedIn) {
            cartItemsContainer.innerHTML = "<p>Você precisa estar logado para ver seu carrinho.</p>";
            // Zera os totais
            if (subtotalSpan) subtotalSpan.innerText = 'R$ 0,00';
            if (totalSpan) totalSpan.innerText = 'R$ 0,00';
            return;
        }

        if (data.itens.length === 0) {
            cartItemsContainer.innerHTML = "<p>Seu carrinho está vazio.</p>";
        } else {
            data.itens.forEach(item => {
                // Se o preço for null (bug de antes), trata como 0 para evitar NaN
                const precoUnitario = parseFloat(item.preco_unitario_salvo) || 0;
                const itemSubtotal = precoUnitario * item.quantidade;

                // CORREÇÃO: Trata 'null' vindo do banco (para produtos sem variação)
                const descricao = item.variacao_descricao ? `<p class="texto-cinza">${item.variacao_descricao}</p>` : '';
                
                const itemHTML = `
                    <div class="item-carrinho" data-item-id="${item.carrinho_id}">
                        <img src="${item.produto_imagem || 'assets/img/iconebebida.png'}" alt="${item.produto_nome}" class="item-imagem">
                        
                        <div class="item-detalhes">
                            <h3>${item.produto_nome}</h3>
                            ${descricao} <p class="preco-item">R$ ${precoUnitario.toFixed(2).replace('.', ',')}</p>
                        </div>

                        <div class="item-controles">
                            <div class="seletor-quantidade">
                                <button class="btn-qtd btn-decrease" onclick="updateQuantity(${item.carrinho_id}, ${item.quantidade - 1})">-</button>
                                <span class="quantidade">${item.quantidade}</span>
                                <button class="btn-qtd btn-increase" onclick="updateQuantity(${item.carrinho_id}, ${item.quantidade + 1})">+</button>
                            </div>
                            <span class="preco-subtotal">R$ ${itemSubtotal.toFixed(2).replace('.', ',')}</span>
                        </div>
                    </div>
                `;
                cartItemsContainer.innerHTML += itemHTML;
            });
        }

        // Atualiza totais
        if (subtotalSpan) subtotalSpan.innerText = `R$ ${data.precoTotal.toFixed(2).replace('.', ',')}`;
        if (totalSpan) totalSpan.innerText = `R$ ${data.precoTotal.toFixed(2).replace('.', ',')}`;
    
    } catch (error) {
        console.error('Erro ao renderizar carrinho:', error);
        cartItemsContainer.innerHTML = "<p>Erro ao carregar o carrinho. Tente novamente.</p>";
    }
}

async function updateQuantity(itemId, novaQuantidade) {
    try {
        await fetch('carrinho_atualizar_qtd.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: itemId, quantidade: novaQuantidade })
        });
        
        renderCartItems();

        updateCartIcon();

    } catch (error) {
        console.error('Erro ao atualizar quantidade:', error);
    }
}

async function clearCart() {
    if (!confirm('Tem certeza que deseja limpar o carrinho?')) {
        return;
    }

    try {
        await fetch('carrinho_limpar.php');
        
        renderCartItems();

        updateCartIcon();

    } catch (error) {
        console.error('Erro ao limpar carrinho:', error);
    }
}

function goToPayment() {
    alert("Redirecionando para pagamento...");

}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('cartItemsContainer')) {
        renderCartItems();
    }
});