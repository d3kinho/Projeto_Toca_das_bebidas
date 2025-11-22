<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_nome']) && isset($_SESSION['usuario_perfil'])) {
    echo json_encode([
        'loggedIn' => true,
        'id' => $_SESSION['usuario_id'],
        'login' => $_SESSION['usuario_login'],
        'nome' => $_SESSION['usuario_nome'],
        'perfil' => $_SESSION['usuario_perfil']
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>