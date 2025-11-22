<?php
session_start();
header('Content-Type: application/json');
include("conexao.php");

$response = [
    'success' => false,
    'message' => 'Ocorreu um erro desconhecido.'
];

if (!isset($_SESSION['usuario_id'])) {
    $response['message'] = 'Usuário não está logado.';
    echo json_encode($response);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $acao = trim($_POST['acao'] ?? '');

    if ($acao === 'ativar') {
        $cep_digitado = trim($_POST['cep'] ?? '');
        
        $cep_limpo = preg_replace('/[^0-9]/', '', $cep_digitado);
        if (strlen($cep_limpo) !== 8) {
            $response['message'] = 'CEP inválido. Digite 8 dígitos.';
            echo json_encode($response);
            exit;
        }

        $stmt_check = $conn->prepare("SELECT endereco FROM usuarios WHERE id = ?");
        $stmt_check->bind_param("i", $usuario_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $usuario = $result->fetch_assoc();
        $stmt_check->close();

        $cep_formatado = substr($cep_limpo, 0, 5) . '-' . substr($cep_limpo, 5);
        
        $stmt = $conn->prepare("UPDATE usuarios SET autenticacao_2fa = TRUE, cep = ? WHERE id = ?");
        $stmt->bind_param("si", $cep_formatado, $usuario_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Autenticação de dois fatores ativada com sucesso! Lembre-se do seu CEP.';
            $response['ativado'] = true;
        } else {
            $response['message'] = 'Erro ao ativar 2FA: ' . $stmt->error;
        }
        $stmt->close();
    }
    
    elseif ($acao === 'desativar') {
        $stmt = $conn->prepare("UPDATE usuarios SET autenticacao_2fa = FALSE WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);

        if ($stmt->execute()) {
            $delete_stmt = $conn->prepare("DELETE FROM sessoes_2fa WHERE id_usuario = ?");
            $delete_stmt->bind_param("i", $usuario_id);
            $delete_stmt->execute();
            $delete_stmt->close();

            $response['success'] = true;
            $response['message'] = 'Autenticação de dois fatores desativada.';
            $response['ativado'] = false;
        } else {
            $response['message'] = 'Erro ao desativar 2FA: ' . $stmt->error;
        }
        $stmt->close();
    }
    
    elseif ($acao === 'status') {
        $stmt = $conn->prepare("SELECT autenticacao_2fa, cep FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            $response['success'] = true;
            $response['ativado'] = (bool)$usuario['autenticacao_2fa'];
            $response['tem_cep'] = !empty($usuario['cep']); 
        } else {
             $response['message'] = 'Usuário não encontrado.';
        }
        $stmt->close();
    }
}

$conn->close();
echo json_encode($response);
?>