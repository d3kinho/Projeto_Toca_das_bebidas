document.addEventListener('DOMContentLoaded', async () => { 
    
    if (typeof updateCartIcon === 'function') {
        updateCartIcon();
    }

    try {
        const response = await fetch('verificador_sessao.php');
        const sessaoData = await response.json();

        if (!sessaoData.loggedIn) {
            return;
        }

        const usuarioLogado = sessaoData; 
        const openModalBtn = document.getElementById('openModalBtn');
        const openModalBtnMobile = document.getElementById('openModalBtnMobile');
        const loginModal = document.getElementById('loginModal');

        if (!openModalBtn || !loginModal) return;

        const handleAccountClick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const modalContent = loginModal.querySelector('.modal-content');
            const primeiroNome = usuarioLogado.nome.split(' ')[0];

            let adminButtonHTML = '';
            if (usuarioLogado.perfil === 'master') {
                adminButtonHTML = '<a href="admin.php" class="login-submit" style="display:block; text-align:center; background-color: #007bff; margin-bottom: 10px; text-decoration:none; line-height: normal;">Painel Admin</a>';
            }

            modalContent.innerHTML = `
                <img src="assets/img/img_reserve/close_black_36dp.png" alt="Fechar" class="close-btn-account" style="position: absolute; top: 15px; right: 20px; width: 24px; cursor: pointer;">
                <h2>Sua Conta</h2>
                <div class="user-info-modal">
                    <p>Bem-vindo(a) de volta,</p>
                    <h3>${primeiroNome}</h3> 
                    
                    ${adminButtonHTML} 
                    
                    <button onclick="abrirConfig2FA()" class="login-submit" style="background-color: #17a2b8; margin-bottom: 10px;">
                        🔐 Segurança (2FA)
                    </button>

                    <button id="logoutBtn" class="login-submit" style="background-color: #dc3545;">Sair</button>
                </div>
            `;
            const closeBtn = modalContent.querySelector('.close-btn-account');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    loginModal.classList.add('hidden');
                });
            }

            const logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', () => {
                    window.location.href = 'logout.php';
                });
            }

            loginModal.classList.remove('hidden');
        };

        const newOpenBtn = openModalBtn.cloneNode(true);
        openModalBtn.parentNode.replaceChild(newOpenBtn, openModalBtn);
        newOpenBtn.addEventListener('click', handleAccountClick);

        if (openModalBtnMobile) {
            const newOpenBtnMobile = openModalBtnMobile.cloneNode(true);
            openModalBtnMobile.parentNode.replaceChild(newOpenBtnMobile, openModalBtnMobile);
            newOpenBtnMobile.addEventListener('click', handleAccountClick);
        }

        loginModal.addEventListener('click', (e) => {
            if (e.target === loginModal) {
                loginModal.classList.add('hidden');
            }
        });

    } catch (error) {
        console.error('Erro ao verificar sessão:', error);
    }
});