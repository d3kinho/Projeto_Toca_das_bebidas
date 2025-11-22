<?php
session_start();
header('Content-Type: application/json');
include("conexao.php");

$response = [
    'success' => false,
    'message' => 'Ocorreu um erro desconhecido.'
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = trim($_POST['token']);
    $resposta = trim($_POST['resposta']);

    if (empty($token) || empty($resposta)) {
        $response['message'] = 'Token e resposta são obrigatórios.';
        echo json_encode($response);
        exit;
    }

    $sql = "SELECT s.id, s.id_usuario, s.pergunta_tipo, s.tentativas, s.expires_at,
                   u.nome_materno, u.data_nascimento, u.cep, u.nome, u.login, u.perfil
            FROM sessoes_2fa s
            JOIN usuarios u ON s.id_usuario = u.id
            WHERE s.token = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'Sessão inválida ou expirada.';
        echo json_encode($response);
        $stmt->close(); $conn->close(); exit;
    }

    $sessao = $result->fetch_assoc();
    $stmt->close();

    if (strtotime($sessao['expires_at']) < time()) {
        $conn->query("DELETE FROM sessoes_2fa WHERE id = {$sessao['id']}");
        $response['message'] = 'Sessão expirada.';
        echo json_encode($response); $conn->close(); exit;
    }
    if ($sessao['tentativas'] >= 3) {
        $conn->query("DELETE FROM sessoes_2fa WHERE id = {$sessao['id']}");
        $response['message'] = 'Tentativas excedidas.';
        $response['excedeu_tentativas'] = true;
        echo json_encode($response); $conn->close(); exit;
    }

    $resposta_correta = false;
    switch ($sessao['pergunta_tipo']) {
        case 'mae':
            $resposta_correta = (mb_strtolower(trim($sessao['nome_materno']), 'UTF-8') === mb_strtolower(trim($resposta), 'UTF-8'));
            break;
        case 'nascimento':
            $data_bd = str_replace(['-', '/'], '', $sessao['data_nascimento']);
            $resp_limpa = str_replace(['-', '/'], '', trim($resposta));
            $data_br = substr($data_bd, 6, 2) . substr($data_bd, 4, 2) . substr($data_bd, 0, 4);
            if ($resp_limpa === $data_bd || $resp_limpa === $data_br) $resposta_correta = true;
            break;
        case 'cep':
            $cep_bd = preg_replace('/[^0-9]/', '', $sessao['cep']);
            $resp_limpa = preg_replace('/[^0-9]/', '', $resposta);
            if (!empty($cep_bd) && $cep_bd === $resp_limpa) $resposta_correta = true;
            break;
    }

    if ($resposta_correta) {
        $_SESSION['usuario_id'] = $sessao['id_usuario'];
        $_SESSION['usuario_login'] = $sessao['login'];
        $_SESSION['usuario_nome'] = $sessao['nome'];
        $_SESSION['usuario_perfil'] = $sessao['perfil'];

        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt_log = $conn->prepare("INSERT INTO logs_atividades (usuario_id, acao, ip) VALUES (?, 'Login (2FA)', ?)");
        $stmt_log->bind_param("is", $sessao['id_usuario'], $ip);
        $stmt_log->execute();
        $stmt_log->close();

        $conn->query("DELETE FROM sessoes_2fa WHERE id = {$sessao['id']}");
        $response['success'] = true;
        $response['message'] = 'Autenticação realizada com sucesso!';
    } else {

        $novas = $sessao['tentativas'] + 1;
        $conn->query("UPDATE sessoes_2fa SET tentativas = $novas WHERE id = {$sessao['id']}");
        
        if ((3 - $novas) > 0) {
            $response['message'] = "Resposta incorreta. Restam " . (3 - $novas) . " tentativas.";
            $response['tentativas_restantes'] = 3 - $novas;
        } else {
            $conn->query("DELETE FROM sessoes_2fa WHERE id = {$sessao['id']}");
            $response['message'] = 'Tentativas esgotadas.';
            $response['excedeu_tentativas'] = true;
        }
    }
}
$conn->close();
echo json_encode($response);
?>