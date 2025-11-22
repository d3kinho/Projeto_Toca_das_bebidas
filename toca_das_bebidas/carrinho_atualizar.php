<?php
session_start();
include('conexao.php');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Erro ao atualizar.'];

if (!isset($_SESSION['usuario_id'])) {
    $response['message'] = 'Usuário não logado.';
    echo json_encode($response);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['carrinho_id']) || !isset($data['quantidade'])) {
    $response['message'] = 'Dados inválidos.';
    echo json_encode($response);
    exit;
}

$carrinho_id = $data['carrinho_id'];
$nova_quantidade = $data['quantidade'];

if ($nova_quantidade > 0) {
    $stmt = $conn->prepare("UPDATE carrinho_itens SET quantidade = ? WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("iii", $nova_quantidade, $carrinho_id, $usuario_id);
} else {
    $stmt = $conn->prepare("DELETE FROM carrinho_itens WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $carrinho_id, $usuario_id);
}
$stmt->execute();

$response['success'] = true;
echo json_encode($response);
$conn->close();
?>