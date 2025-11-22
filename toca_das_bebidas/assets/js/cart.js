// ============================================
// cart.js - ADICIONAR AO CARRINHO (menu.php)
// ============================================

/**
 * Adiciona um item ao carrinho via fetch para o PHP, usando IDs.
 */
async function addToCart(produto_id, variacao_id, preco, nome_produto) {
    try {
        const response = await fetch('carrinho_adicionar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                produto_id: produto_id,
                variacao_id: variacao_id, // Pode ser null
                preco: preco
            })
        });

        const data = await response.json();

        if (data.success) {
            // Usa o nome_produto apenas para a notificação
            showTopNotification(`"${nome_produto}" foi adicionado ao carrinho!`, 'success');
        } else if (data.message === 'Usuário não logado.') {
            const modal = document.getElementById('loginModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        } else {
            showTopNotification(data.message, 'error');
        }
        
        // Atualiza os ícones do carrinho em tempo real
        updateCartIcon();

    } catch (error) {
        console.error('Erro ao adicionar ao carrinho:', error);
        showTopNotification('Erro de conexão ao adicionar item.', 'error');
    }
}

/**
 * Atualiza os ícones do carrinho (header e mobile) buscando dados do PHP.
 */
async function updateCartIcon() {
    try {
        const response = await fetch('carrinho_listar.php');
        const data = await response.json();

        let totalItens = 0;
        let precoTotal = 0.00;

        if (data.loggedIn) {
            totalItens = data.totalItens;
            precoTotal = data.precoTotal;
        }

        const cartTotalDesktop = document.querySelector('.cart-total');
        const cartCountDesktop = document.querySelector('.cart-count');
        const cartTotalMobile = document.querySelector('.cart-total-mobile span');
        const cartCountMobileBadge = document.querySelector('.cart-count-mobile-badge');
        const mobileCartBar = document.querySelector('.mobile-cart-bar');

        const formattedPrice = `R$ ${precoTotal.toFixed(2).replace('.', ',')}`;

        if (cartTotalDesktop && cartCountDesktop) {
            cartTotalDesktop.textContent = formattedPrice;
            cartCountDesktop.textContent = `${totalItens} ${totalItens === 1 ? 'item' : 'itens'}`;
        }

        if (cartTotalMobile && cartCountMobileBadge && mobileCartBar) {
            if (totalItens > 0) {
                mobileCartBar.style.display = 'block';
                cartTotalMobile.textContent = formattedPrice;
                cartCountMobileBadge.textContent = totalItens;
            } else {
                mobileCartBar.style.display = 'none';
            }
        }

    } catch (error) {
        console.error('Erro ao atualizar ícone do carrinho:', error);
    }
}

/**
 * Lógica da página de menu (seleção de tamanho, botão comprar).
 */
document.addEventListener('DOMContentLoaded', function() {
    // Só executa essa lógica se NÃO estivermos na página shopcar
    if (document.getElementById('cartItemsContainer')) {
        return;
    }
    
    // Atualiza o ícone do carrinho assim que a página carrega
    updateCartIcon();

    const productCards = document.querySelectorAll('.pizza-card');

    productCards.forEach(card => {
        const sizeButtons = card.querySelectorAll('.size-btn');
        const buyButton = card.querySelector('.buy-button');
        const errorMessageDiv = card.querySelector('.product-error-message');
        
        // Pega os dados principais do card
        const produto_id = card.dataset.produtoId;
        const nome_produto = card.querySelector('.pizza-info h3').innerText;

        sizeButtons.forEach(button => {
            button.addEventListener('click', () => {
                sizeButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                if (errorMessageDiv) errorMessageDiv.classList.remove('show');

                const selectedPrice = parseFloat(button.dataset.price);
                const formattedPrice = `R$ ${selectedPrice.toFixed(2).replace('.', ',')}`;
                
                if (buyButton) {
                    buyButton.textContent = formattedPrice;
                    buyButton.dataset.priceText = formattedPrice; 
                }
            });
        });

        if (buyButton) {
            buyButton.addEventListener('mouseover', () => {
                if (buyButton.dataset.priceText) {
                    buyButton.textContent = 'COMPRAR';
                }
            });

            buyButton.addEventListener('mouseout', () => {
                if (buyButton.dataset.priceText) {
                    buyButton.textContent = buyButton.dataset.priceText;
                }
            });
            
            buyButton.addEventListener('click', () => {
                let variacao_id = null;
                let preco = 0;

                if (card.classList.contains('sem-tamanho')) {
                    // --- Produto de preço único ---
                    variacao_id = null;
                    preco = parseFloat(buyButton.dataset.price);
                } else {
                    // --- Produto com variações ---
                    const activeSizeButton = card.querySelector('.size-btn.active');
                    if (activeSizeButton) {
                        variacao_id = activeSizeButton.dataset.variacaoId;
                        preco = parseFloat(activeSizeButton.dataset.price);
                    } else {
                        // Nenhuma variação selecionada
                        if (errorMessageDiv) {
                            errorMessageDiv.textContent = `Por favor, selecione um tamanho.`;
                            errorMessageDiv.classList.add('show');
                        }
                        const sizeSelector = card.querySelector('.size-selector');
                        if (sizeSelector) {
                            sizeSelector.classList.add('shake-error');
                            setTimeout(() => {
                                sizeSelector.classList.remove('shake-error');
                            }, 500); 
                        }
                        return; // Para a execução
                    }
                }
                // Envia para o carrinho
                addToCart(produto_id, variacao_id, preco, nome_produto);
            });
        }
    });
});