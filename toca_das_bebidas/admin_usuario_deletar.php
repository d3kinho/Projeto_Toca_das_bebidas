<?php
session_start();
include('verificar_admin.php'); // SEGURANÇA!
include('conexao.php');

$feedback_tipo = 'erro';
$feedback_msg = 'Erro desconhecido.';

if (isset($_GET['id'])) {
    $id_para_deletar = $_GET['id'];
    $id_do_admin_logado = $_SESSION['usuario_id'];

    if ($id_para_deletar == $id_do_admin_logado) {
        $feedback_tipo = 'erro';
        $feedback_msg = 'Você não pode deletar a si mesmo!';
    } else {
        try {
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $id_para_deletar);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $feedback_tipo = 'sucesso';
                    $feedback_msg = 'Usuário deletado com sucesso!';
                } else {
                    $feedback_tipo = 'erro';
                    $feedback_msg = 'Usuário não encontrado ou já deletado.';
                }
            }
            $stmt->close();
            
        } catch (Exception $e) {
            $feedback_msg = 'Erro ao deletar: ' . $e->getMessage();
        }
    }
} else {
    $feedback_msg = 'Nenhum ID de usuário fornecido.';
}

$conn->close();

$_SESSION['feedback_admin'] = "<p class='admin-aviso {$feedback_tipo}'>{$feedback_msg}</p>";

header('Location: admin_usuarios.php');
exit;
?>