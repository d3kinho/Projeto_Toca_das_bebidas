<?php

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['usuario_perfil'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['usuario_perfil'] !== 'master') {
    header('Location: index.php');
    exit;
}
?>