<?php
session_start();
include('conexao.php');
header('Content-Type: application/json');

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $conn->prepare("DELETE FROM carrinho_itens WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
}

echo json_encode(['success' => true]);
$conn->close();
?>