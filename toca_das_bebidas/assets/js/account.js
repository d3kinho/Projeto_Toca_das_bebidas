document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('cadastroForm');
    if (!form) return;

    const formSteps = Array.from(form.querySelectorAll('.form-step'));
    const errorMessageDiv = document.getElementById('error-message');
    let currentStep = formSteps.findIndex(step => step.classList.contains('form-step-active'));

    const mascaraCPF = (value) => {
        return value
            .replace(/\D/g, '')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})/, '$1-$2')
            .replace(/(-\d{2})\d+?$/, '$1');
    };

    const mascaraCelular = (value) => {
        return value
            .replace(/\D/g, '')
            .replace(/^(\d{2})(\d)/g, '($1) $2')
            .replace(/(\d{5})(\d{4})/, '$1-$2')
            .replace(/(-\d{4})\d+?$/, '$1');
    };

    const mascaraFixo = (value) => {
        return value
            .replace(/\D/g, '')
            .replace(/^(\d{2})(\d)/g, '($1) $2')
            .replace(/(\d{4})(\d{4})/, '$1-$2')
            .replace(/(-\d{4})\d+?$/, '$1');
    };

    const inputCPF = document.getElementById('cpf');
    const inputCelular = document.getElementById('celular');
    const inputFixo = document.getElementById('fixo');

    if (inputCPF) {
        inputCPF.addEventListener('input', (e) => {
            e.target.value = mascaraCPF(e.target.value);
        });
    }

    if (inputCelular) {
        inputCelular.addEventListener('input', (e) => {
            e.target.value = mascaraCelular(e.target.value);
        });
    }

    if (inputFixo) {
        inputFixo.addEventListener('input', (e) => {
            e.target.value = mascaraFixo(e.target.value);
        });
    }

    const showStep = (stepIndex) => {
        formSteps.forEach(step => step.classList.remove('form-step-active'));
        formSteps[stepIndex].classList.add('form-step-active');
    };

    const showErrorMessage = (message) => {
        errorMessageDiv.textContent = message;
        errorMessageDiv.classList.add('show');
    };

    const hideErrorMessage = () => {
        errorMessageDiv.textContent = '';
        errorMessageDiv.classList.remove('show');
    };

    const isEmailValid = (email) => {
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        return regex.test(email);
    };

    const isCpfValid = (cpf) => {
        const regex = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
        return regex.test(cpf);
    };

    const isCelularValid = (celular) => {
        const regex = /^\(\d{2}\)\s\d{5}-\d{4}$/;
        return regex.test(celular);
    };

    const isFixoValid = (fixo) => {
        if (!fixo) return true;
        const regex = /^\(\d{2}\)\s\d{4}-\d{4}$/;
        return regex.test(fixo);
    };

    const isPasswordStrong = (password) => {
        const regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;
        return regex.test(password);
    };

    const validateStep = (stepIndex) => {
        hideErrorMessage();
        let isValid = true;
        const currentStepFields = formSteps[stepIndex].querySelectorAll('input, select');

        for (const field of currentStepFields) {
            field.classList.remove('invalid');

            if (field.required && !field.value.trim()) {
                showErrorMessage('Por favor, preencha todos os campos obrigatórios.');
                field.classList.add('invalid');
                return false;
            }

            if (field.value) {
                let fieldIsValid = true;
                let errorMessage = '';

                switch (field.id) {
                    case 'email':
                        if (!isEmailValid(field.value)) {
                            fieldIsValid = false;
                            errorMessage = 'Por favor, insira um e-mail válido (ex: nome@email.com).';
                        }
                        break;
                    case 'cpf':
                        if (!isCpfValid(field.value)) {
                            fieldIsValid = false;
                            errorMessage = 'CPF incompleto. O formato deve ser 000.000.000-00.';
                        }
                        break;
                    case 'celular':
                        if (!isCelularValid(field.value)) {
                            fieldIsValid = false;
                            errorMessage = 'Celular incompleto. O formato deve ser (00) 00000-0000.';
                        }
                        break;
                    case 'fixo':
                        if (!isFixoValid(field.value)) {
                            fieldIsValid = false;
                            errorMessage = 'Telefone fixo incompleto. O formato deve ser (00) 0000-0000.';
                        }
                        break;
                    case 'senha':
                        if (!isPasswordStrong(field.value)) {
                            fieldIsValid = false;
                            errorMessage = 'A senha deve ter no mínimo 8 caracteres, com uma letra maiúscula, uma minúscula e um número.';
                        }
                        break;
                }

                if (!fieldIsValid) {
                    showErrorMessage(errorMessage);
                    field.classList.add('invalid');
                    return false;
                }
            }
        }

        if (stepIndex === formSteps.length - 1) {
            const senha = form.querySelector('#senha');
            const confirmarSenha = form.querySelector('#confirmar-senha');
            if (senha && confirmarSenha && senha.value !== confirmarSenha.value) {
                showErrorMessage('As senhas não coincidem. Por favor, verifique.');
                senha.classList.add('invalid');
                confirmarSenha.classList.add('invalid');
                return false;
            }
        }

        return true;
    };

    form.addEventListener('click', (e) => {
        const action = e.target.dataset.action;
        if (!action) return;

        if (action === 'next') {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        } else if (action === 'back') {
            currentStep--;
            showStep(currentStep);
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (validateStep(currentStep)) {
            
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';

            fetch('cadastroUsuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const nome = formData.get('nome'); 
                    
                    if (typeof showTopNotification === 'function') {
                         showTopNotification(`Cadastro realizado com sucesso, ${nome.split(' ')[0]}!`, 'success');
                    } else if (typeof showNotification === 'function') {
                         showNotification(`Cadastro realizado com sucesso, ${nome.split(' ')[0]}!`, 'success');
                    } else {
                         alert(`Cadastro realizado com sucesso!`);
                    }
                    
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2500);

                } else {
                    showErrorMessage(data.message);
                    
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Erro no fetch:', error);
                showErrorMessage('Não foi possível conectar ao servidor. Tente novamente.');
                
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            });
        }
    });

    showStep(currentStep);
});

function menuShow() {
    let menuMobile = document.querySelector('.menu-mobile');
    if (menuMobile.classList.contains('open')) {
        menuMobile.classList.remove('open');
    } else {
        menuMobile.classList.add('open');
    }
}