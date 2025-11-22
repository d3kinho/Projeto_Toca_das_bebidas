<?php
session_start();
include('verificar_admin.php');
include('conexao.php');

$mensagem = "";
$usuario = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $login = trim($_POST['login']);
    $data_nascimento = trim($_POST['data_nascimento']);
    $sexo = trim($_POST['sexo']);
    $nome_materno = trim($_POST['nome_materno']);
    $cpf = trim($_POST['cpf']);
    $celular = trim($_POST['celular']);
    $telefone_fixo = trim($_POST['telefone_fixo']);
    $endereco = trim($_POST['endereco']);
    $perfil = trim($_POST['perfil']);

    if (empty($nome) || empty($email) || empty($login) || empty($cpf) || empty($perfil) || empty($id)) {
        $mensagem = "<p class='admin-aviso erro'>Erro: Todos os campos, exceto telefone fixo, são obrigatórios.</p>";
    } else {
        
        $conn->begin_transaction();
        $sucesso_total = true;
        $erro_msg = "";

        try {
            $stmt = $conn->prepare("
                UPDATE usuarios SET 
                    nome = ?, email = ?, login = ?, data_nascimento = ?, sexo = ?, 
                    nome_materno = ?, cpf = ?, celular = ?, telefone_fixo = ?, 
                    endereco = ?, perfil = ?
                WHERE 
                    id = ?
            ");
            
            $stmt->bind_param("sssssssssssi", 
                $nome, $email, $login, $data_nascimento, $sexo, 
                $nome_materno, $cpf, $celular, $telefone_fixo, 
                $endereco, $perfil, $id
            );

            if (!$stmt->execute()) {
                $sucesso_total = false;
                $erro_msg = $stmt->error;
            }
            $stmt->close();

        } catch (Exception $e) {
            $sucesso_total = false;

            if ($conn->errno == 1062) { 
                $erro_msg = "Erro: Já existe um usuário com este Email, Login ou CPF.";
            } else {
                $erro_msg = $e->getMessage();
            }
        }

        if ($sucesso_total) {
            $conn->commit();
            $mensagem = "<p class='admin-aviso sucesso'>Usuário '{$nome}' atualizado com sucesso! <a href='admin_usuarios.php'>Voltar para a lista</a></p>";
        } else {
            $conn->rollback();
            $mensagem = "<p class='admin-aviso erro'>Erro ao atualizar: {$erro_msg}</p>";
        }
    }
    $id_para_buscar = $id;

} else {
    $id_para_buscar = $_GET['id'] ?? null;
    if (empty($id_para_buscar)) {
        header('Location: admin_usuarios.php');
        exit;
    }
}

$stmt_load = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt_load->bind_param("i", $id_para_buscar);
$stmt_load->execute();
$result = $stmt_load->get_result();

if ($result->num_rows === 0) {
    header('Location: admin_usuarios.php');
    exit;
}
$usuario = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
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
                <h1>Editar Usuário</h1>
            </div>
            <div class="nav-section nav-list">
                <ul>
                    <li class="nav-item"><a href="admin_usuarios.php" class="nav-link">Voltar (Usuários)</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Sair</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="admin-container" style="max-width: 800px;"> <?php echo $mensagem; ?>

            <form action="admin_usuario_editar.php" method="POST" class="admin-form">
                
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($usuario['nome']); ?>">
                </div>
                <div class="form-group">
                    <label for="nome_materno">Nome Materno</label>
                    <input type="text" id="nome_materno" name="nome_materno" required value="<?php echo htmlspecialchars($usuario['nome_materno']); ?>">
                </div>
                <div class="form-group">
                    <label for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" required value="<?php echo htmlspecialchars($usuario['data_nascimento']); ?>">
                </div>
                <div class="form-group">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" name="sexo" required>
                        <option value="masculino" <?php if($usuario['sexo'] == 'masculino') echo 'selected'; ?>>Masculino</option>
                        <option value="feminino" <?php if($usuario['sexo'] == 'feminino') echo 'selected'; ?>>Feminino</option>
                        <option value="outro" <?php if($usuario['sexo'] == 'outro') echo 'selected'; ?>>Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cpf">CPF (Formato: 000.000.000-00)</label>
                    <input type="text" id="cpf" name="cpf" required value="<?php echo htmlspecialchars($usuario['cpf']); ?>">
                </div>
                <div class="form-group">
                    <label for="celular">Celular (Formato: (00) 00000-0000)</label>
                    <input type="text" id="celular" name="celular" required value="<?php echo htmlspecialchars($usuario['celular']); ?>">
                </div>
                <div class="form-group">
                    <label for="telefone_fixo">Telefone Fixo (Opcional)</label>
                    <input type="text" id="telefone_fixo" name="telefone_fixo" value="<?php echo htmlspecialchars($usuario['telefone_fixo']); ?>">
                </div>
                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <textarea id="endereco" name="endereco" rows="3" required><?php echo htmlspecialchars($usuario['endereco']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($usuario['email']); ?>">
                </div>
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" required value="<?php echo htmlspecialchars($usuario['login']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="perfil">Perfil do Usuário</label>
                    <select id="perfil" name="perfil" required <?php if($usuario['id'] == $_SESSION['usuario_id']) echo 'disabled'; ?>>
                        <option value="comum" <?php if($usuario['perfil'] == 'comum') echo 'selected'; ?>>Comum</option>
                        <option value="master" <?php if($usuario['perfil'] == 'master') echo 'selected'; ?>>Master</option>
                    </select>
                    <?php if($usuario['id'] == $_SESSION['usuario_id']): ?>
                        <p class="admin-form-ajuda" style="color: #dc3545;">Você não pode alterar o seu próprio perfil.</p>
                        <input type="hidden" name="perfil" value="<?php echo $usuario['perfil']; ?>" />
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <a href="admin_usuarios.php" class="btn-admin btn-cinza">Cancelar</a>
                    <button type="submit" class="btn-admin btn-verde">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>