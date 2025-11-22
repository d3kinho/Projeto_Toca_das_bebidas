<?php
session_start();
header('Content-Type: application/json');
include("conexao.php");

$response = [
    'success' => false,
    'message' => 'Ocorreu um erro desconhecido.', 
    'perfil' => 'comum'
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identificador = trim($_POST['identificador']);
    $senha = trim($_POST['senha']);

    if (empty($identificador) || empty($senha)) {
        $response['message'] = 'Preencha todos os campos!';
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, nome, login, senha_hash, perfil, autenticacao_2fa FROM usuarios WHERE login = ? OR email = ?");
    $stmt->bind_param("ss", $identificador, $identificador);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        if (password_verify($senha, $usuario['senha_hash'])) {
            
            if ($usuario['autenticacao_2fa']) {
                $token = bin2hex(random_bytes(32));
                $perguntas = ['mae', 'nascimento', 'cep'];
                $pergunta_tipo = $perguntas[array_rand($perguntas)];
                $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                
                $stmt_2fa = $conn->prepare("INSERT INTO sessoes_2fa (id_usuario, token, pergunta_tipo, expires_at) VALUES (?, ?, ?, ?)");
                $stmt_2fa->bind_param("isss", $usuario['id'], $token, $pergunta_tipo, $expires_at);
                
                if ($stmt_2fa->execute()) {
                    $response['success'] = true;
                    $response['requer_2fa'] = true;
                    $response['token_2fa'] = $token;
                    $response['message'] = 'Login válido. Complete a autenticação de dois fatores.';
                } else {
                    $response['message'] = 'Erro ao criar sessão 2FA.';
                }
                $stmt_2fa->close();

            } else {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_login'] = $usuario['login'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_perfil'] = $usuario['perfil'];

                $ip = $_SERVER['REMOTE_ADDR'];
                $log_stmt = $conn->prepare("INSERT INTO logs_atividades (usuario_id, acao, ip) VALUES (?, 'Login', ?)");
                $log_stmt->bind_param("is", $usuario['id'], $ip);
                $log_stmt->execute();
                $log_stmt->close();

                $response['success'] = true;
                $response['requer_2fa'] = false;
                $response['message'] = 'Login realizado com sucesso!';
                $response['usuario'] = [
                    'id' => $usuario['id'],
                    'nome' => $usuario['nome'],
                    'login' => $usuario['login'],
                    'perfil' => $usuario['perfil']
                ];
                $response['perfil'] = $usuario['perfil'];
            }
        } else {
            $response['message'] = 'Usuário ou senha incorretos!';
        }
    } else {
        $response['message'] = 'Usuário ou senha incorretos!';
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>