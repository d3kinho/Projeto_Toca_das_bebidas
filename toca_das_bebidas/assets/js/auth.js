let token2FA = null;

document.addEventListener('DOMContentLoaded', function() {
    configurarLogin();
    configurarModal2FA();
    
    const form2FA = document.getElementById('form2FA');
    if (form2FA) {
        form2FA.addEventListener('submit', function(e) {
            e.preventDefault();
            validarResposta2FA();
        });
    }
});

function configurarLogin() {
    const loginForm = document.querySelector('#loginModal form');
    if (!loginForm) return;

    const newLoginForm = loginForm.cloneNode(true);
    loginForm.parentNode.replaceChild(newLoginForm, loginForm);

    newLoginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        await handleLogin(e);
    });

    const toggleBtn = document.querySelector('.toggle-password');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const input = document.getElementById('senha');
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    }
}

async function handleLogin(e) {
    const loginForm = e.target;
    const identificador = loginForm.querySelector('#identificador').value.trim();
    const senha = loginForm.querySelector('#senha').value;
    const errorMessageDiv = loginForm.querySelector('.login-error');
    const submitBtn = loginForm.querySelector('button[type="submit"]');

    if (errorMessageDiv) {
        errorMessageDiv.style.display = 'none';
        errorMessageDiv.textContent = '';
    }
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Verificando...';

    try {
        const formData = new FormData();
        formData.append('identificador', identificador);
        formData.append('senha', senha);

        const response = await fetch('login.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success && data.requer_2fa) {
            token2FA = data.token_2fa;

            document.getElementById('loginModal').classList.add('hidden');
            
            mostrarModal2FA();

        } else if (data.success) {
            showNotification('Login realizado com sucesso!', 'success');
            document.getElementById('loginModal').classList.add('hidden');
            setTimeout(() => window.location.reload(), 1000);

        } else {
            if (errorMessageDiv) {
                errorMessageDiv.textContent = data.message || 'Erro ao fazer login.';
                errorMessageDiv.style.display = 'block';
            }
        }

    } catch (error) {
        console.error('Erro login:', error);
        if (errorMessageDiv) {
            errorMessageDiv.textContent = 'Erro de conexão.';
            errorMessageDiv.style.display = 'block';
        }
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
    }
}

function mostrarModal2FA() {
    const modal = document.getElementById('modal2FA');
    const perguntaEl = document.getElementById('pergunta2FA');
    const tentativasEl = document.getElementById('tentativas2FA');
    const inputResposta = document.getElementById('resposta2FA');
    
    if (!modal) return;

    fetch(`obter_pergunta_2fa.php?token=${token2FA}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                perguntaEl.textContent = data.pergunta;
                tentativasEl.textContent = `Tentativas restantes: ${data.tentativas_restantes}`;
                inputResposta.value = '';
                
                modal.classList.remove('hidden');
                setTimeout(() => inputResposta.focus(), 100);
            } else {
                alert(data.message);
                window.location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erro ao carregar pergunta de segurança.');
        });
}

async function validarResposta2FA() {
    const resposta = document.getElementById('resposta2FA').value.trim();
    const errorDiv = document.getElementById('error2FAValidacao');
    const submitBtn = document.querySelector('#form2FA button');

    if (!resposta) return;

    submitBtn.disabled = true;
    submitBtn.textContent = 'Verificando...';

    try {
        const formData = new FormData();
        formData.append('token', token2FA);
        formData.append('resposta', resposta);

        const response = await fetch('verificar_2fa.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            showNotification('Autenticação realizada!', 'success');
            document.getElementById('modal2FA').classList.add('hidden');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            if (errorDiv) {
                errorDiv.textContent = data.message;
                errorDiv.style.display = 'block';
            }
            
            if (data.tentativas_restantes !== undefined) {
                document.getElementById('tentativas2FA').textContent = `Tentativas restantes: ${data.tentativas_restantes}`;
            }

            if (data.excedeu_tentativas) {
                setTimeout(() => window.location.reload(), 2000);
            }
        }
    } catch (error) {
        console.error(error);
        if (errorDiv) {
            errorDiv.textContent = 'Erro de conexão.';
            errorDiv.style.display = 'block';
        }
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Verificar';
    }
}

function abrirConfig2FA() {
    const modal = document.getElementById('config2FAModal');
    const content = document.getElementById('config2FAContent');
    
    // Busca status atual
    fetch('config_2fa.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'acao=status'
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
            return;
        }

        if (data.ativado) {
            content.innerHTML = `
                <h2 style="color: #28a745;">Segurança Ativa ✓</h2>
                <p>A autenticação de dois fatores está <strong>ATIVADA</strong>.</p>
                <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">Sua conta está protegida com perguntas de segurança (Mãe, Nascimento, CEP).</p>
                <button onclick="desativar2FA()" class="login-submit" style="background-color: #dc3545;">Desativar 2FA</button>
            `;
        } else {
            content.innerHTML = `
                <h2>Ativar Segurança 2FA</h2>
                <p style="margin-bottom: 15px;">Adicione uma camada extra de proteção à sua conta.</p>
                
                <form onsubmit="event.preventDefault(); ativar2FA();">
                    <div class="form-group">
                        <label for="cepConfirmacao" style="font-weight: bold; display: block; margin-bottom: 5px;">Confirme seu CEP:</label>
                        <input type="text" id="cepConfirmacao" placeholder="Ex: 12345-678" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        <small style="color: #666; display: block; margin-top: 5px;">Digite o CEP cadastrado no seu endereço.</small>
                    </div>
                    <div id="msgConfig2FA" style="margin: 10px 0; color: #dc3545; display: none;"></div>
                    <button type="submit" class="login-submit">Ativar Agora</button>
                </form>
            `;
        }
        modal.classList.remove('hidden');
    });
}

function ativar2FA() {
    const cep = document.getElementById('cepConfirmacao').value;
    const msgDiv = document.getElementById('msgConfig2FA');

    fetch('config_2fa.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `acao=ativar&cep=${encodeURIComponent(cep)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('2FA Ativado com sucesso!', 'success');
            document.getElementById('config2FAModal').classList.add('hidden');
        } else {
            msgDiv.textContent = data.message;
            msgDiv.style.display = 'block';
        }
    });
}

function desativar2FA() {
    if (!confirm('Tem certeza que deseja remover a proteção extra da sua conta?')) return;

    fetch('config_2fa.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'acao=desativar'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('2FA Desativado.', 'info');
            document.getElementById('config2FAModal').classList.add('hidden');
        } else {
            alert(data.message);
        }
    });
}

function configurarModal2FA() {
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.add('hidden');
            }
        });
    });
}

function showNotification(msg, type) {
    const bar = document.getElementById('top-notification');
    const span = document.getElementById('top-notification-message');
    
    if (bar && span) {
        span.textContent = msg;
        bar.className = `top-notification-bar top-notification-bar--${type} show`;
        
        setTimeout(() => {
            bar.classList.remove('show');
        }, 3000);
    }
}
window.abrirConfig2FA = abrirConfig2FA;
window.ativar2FA = ativar2FA;
window.desativar2FA = desativar2FA;