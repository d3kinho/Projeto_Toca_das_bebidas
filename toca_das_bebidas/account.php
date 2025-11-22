<?php 
session_start();
include("conexao.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <script src="assets/js/account.js" defer></script>
</head>
<body class="pagina-cadastro">
    <header>
    <nav class="nav-bar">
        <div class="nav-section">
            <ul class="nav-list">
                <li class="nav-item"><a href="index.php" class="nav-link">Início</a></li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="logo">
                <img id="nav-logo" src="assets/img/iconebebida.png" alt="Logo">
                <h1>Toca das Bebidas</h1>
            </div>
        </div>

        <div class="nav-section">
            </div>
    </nav>
</header>

    <main class="main-content">
        <div class="form-container">
            <form action="" method="post" id="cadastroForm" novalidate>
                <div id="error-message" class="error-message"></div>

                <!-- etapa 1 -->
                <div class="form-step form-step-active">
                    <h2>Crie sua Conta</h2>
                    <p>Para começar, informe seu melhor e-mail.</p>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="step-buttons">
                        <button type="reset" class="btn btn-light">Limpar</button>
                        <button type="button" class="btn btn-primary" data-action="next">Próximo</button>
                    </div>
                </div>

                <!-- etapa 2 -->
                <div class="form-step">
                    <h2>Dados Pessoais</h2>
                    <p>Queremos conhecer um pouco mais sobre você.</p>
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="data-nascimento">Data de Nascimento</label>
                        <input type="date" id="data-nascimento" name="data_nascimento" required>
                    </div>
                    <div class="form-group">
                        <label for="sexo">Sexo</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Selecione</option>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nome-materno">Nome Materno</label>
                        <input type="text" id="nome_materno" name="nome_materno" required>
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn btn-secondary" data-action="back">Voltar</button>
                        <button type="reset" class="btn btn-light">Limpar</button>
                        <button type="button" class="btn btn-primary" data-action="next">Próximo</button>
                    </div>
                </div>

                <!-- etapa 3 -->
                <div class="form-step">
                    <h2>Contato e Endereço</h2>
                    <p>Informe seus contatos e endereço de entrega.</p>
                    <div class="form-group">
                        <label for="celular">Telefone Celular</label>
                        <input type="tel" id="celular" name="celular" placeholder="(00) 00000-0000" required>
                    </div>
                    <div class="form-group">
                        <label for="fixo">Telefone Fixo (Opcional)</label>
                        <input type="tel" id="fixo" name="telefone_fixo" placeholder="(00) 0000-0000">
                    </div>
                    <div class="form-group">
                        <label for="endereco">Endereço Completo</label>
                        <input type="text" id="endereco" name="endereco" placeholder="Rua, Número, Bairro, CEP..." required>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn btn-secondary" data-action="back">Voltar</button>
                        <button type="reset" class="btn btn-light">Limpar</button>
                        <button type="button" class="btn btn-primary" data-action="next">Próximo</button>
                    </div>
                </div>

                <!-- etapa 4 -->
                <div class="form-step">
                    <h2>Dados de Acesso</h2>
                    <p>Para finalizar, crie seu login e senha.</p>
                    <div class="form-group">
                        <label for="login">Login</label>
                        <input type="text" id="login" name="login" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha_hash" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmar-senha">Confirmação de Senha</label>
                        <input type="password" id="confirmar-senha" name="confirmar_senha" required>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn btn-secondary" data-action="back">Voltar</button>
                        <button type="reset" class="btn btn-light">Limpar</button>
                        <button type="submit" class="btn btn-primary">Enviar Cadastro</button>
                    </div>
                </div>

            </form>
        </div>
    </main>

    <!-- rodapé -->
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
</body>
</html>
