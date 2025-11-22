<?php
session_start();
include('conexao.php');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Usuário não logado.'];

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode($response);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['produto_id']) || !isset($data['preco'])) {
    $response['message'] = 'Dados do produto inválidos.';
    echo json_encode($response);
    exit;
}

$produto_id = $data['produto_id'];
$variacao_id = $data['variacao_id'] ?? null; 
$preco_salvo = $data['preco'];
$quantidade_adicionar = 1;

try {
    if ($variacao_id === null) {
        $sql_check = "SELECT id, quantidade FROM carrinho_itens WHERE usuario_id = ? AND produto_id = ? AND variacao_id IS NULL";
        $check = $conn->prepare($sql_check);
        $check->bind_param("ii", $usuario_id, $produto_id);
    } else {
        $sql_check = "SELECT id, quantidade FROM carrinho_itens WHERE usuario_id = ? AND produto_id = ? AND variacao_id = ?";
        $check = $conn->prepare($sql_check);
        $check->bind_param("iii", $usuario_id, $produto_id, $variacao_id);
    }

    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        $nova_quantidade = $item['quantidade'] + $quantidade_adicionar;
        $item_id = $item['id'];
        
        $stmt = $conn->prepare("UPDATE carrinho_itens SET quantidade = ? WHERE id = ?");
        $stmt->bind_param("ii", $nova_quantidade, $item_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO carrinho_itens (usuario_id, produto_id, variacao_id, quantidade, preco_unitario_salvo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiid", $usuario_id, $produto_id, $variacao_id, $quantidade_adicionar, $preco_salvo);
        $stmt->execute();
    }

    $response['success'] = true;
    $response['message'] = 'Item adicionado ao carrinho!';

} catch (Exception $e) {
    $response['message'] = 'Erro no banco de dados: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>