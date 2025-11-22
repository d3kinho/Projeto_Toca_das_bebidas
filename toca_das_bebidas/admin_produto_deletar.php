<?php
session_start();
include('verificar_admin.php'); // SEGURANÇA!
include('conexao.php');

if (isset($_GET['id'])) {
    $produto_id = $_GET['id'];
    
    try {
        // Prepara para deletar o produto
        $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $produto_id);
        
        if ($stmt->execute()) {
        } else {
        }
        $stmt->close();
        
    } catch (Exception $e) {

    }
}

header('Location: admin.php');
exit;
?>