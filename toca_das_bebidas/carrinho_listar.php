<?php
session_start();
include('conexao.php');
header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'itens' => [],
    'totalItens' => 0,
    'precoTotal' => 0.00
];

if (isset($_SESSION['usuario_id'])) {
    $response['loggedIn'] = true;
    $usuario_id = $_SESSION['usuario_id'];
    
    $sql = "
        SELECT 
            c.id AS carrinho_id,
            c.quantidade,
            c.preco_unitario_salvo,
            p.nome AS produto_nome,
            p.imagem_url AS produto_imagem,
            v.descricao AS variacao_descricao
        FROM 
            carrinho_itens c
        JOIN 
            produtos p ON c.produto_id = p.id
        LEFT JOIN 
            produto_variacoes v ON c.variacao_id = v.id
        WHERE 
            c.usuario_id = ?
        ORDER BY 
            c.id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $totalItens = 0;
    $precoTotal = 0.00;

    while ($item = $result->fetch_assoc()) {
        $response['itens'][] = $item;
        $totalItens += $item['quantidade'];
        $precoTotal += $item['preco_unitario_salvo'] * $item['quantidade'];
    }
    $response['totalItens'] = $totalItens;
    $response['precoTotal'] = $precoTotal;
}

echo json_encode($response);
$conn->close();
?>