<?php
session_start();
include("conexao.php");

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $conn->prepare("INSERT INTO logs_atividades (usuario_id, acao, ip) VALUES (?, 'Logout', ?)");
    $stmt->bind_param("is", $usuario_id, $ip);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

$_SESSION = array();
session_destroy();

header('Location: index.php');
exit;
?>