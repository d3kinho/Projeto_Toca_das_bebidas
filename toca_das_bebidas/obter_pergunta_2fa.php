<?php
session_start();
header('Content-Type: application/json');
include("conexao.php");

$response = [
    'success' => false,
    'message' => 'Ocorreu um erro desconhecido.'
];

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['token'])) {
    $token = trim($_GET['token']);

    if (empty($token)) {
        $response['message'] = 'Token é obrigatório.';
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("SELECT pergunta_tipo, tentativas, expires_at FROM sessoes_2fa WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'Sessão inválida ou expirada.';
        echo json_encode($response);
        $stmt->close();
        $conn->close();
        exit;
    }

    $sessao = $result->fetch_assoc();
    $stmt->close();

    if (strtotime($sessao['expires_at']) < time()) {
        $response['message'] = 'Sessão expirada. Faça login novamente.';
        echo json_encode($response);
        $conn->close();
        exit;
    }

    if ($sessao['tentativas'] >= 3) {
        $response['message'] = 'Número máximo de tentativas excedido. Faça login novamente.';
        $response['excedeu_tentativas'] = true;
        $conn->close();
        exit;
    }

    $perguntas_texto = [
        'mae' => 'Qual o nome da sua mãe?',
        'nascimento' => 'Qual a data do seu nascimento?',
        'cep' => 'Qual o CEP do seu endereço?'
    ];

    $response['success'] = true;
    $response['pergunta'] = $perguntas_texto[$sessao['pergunta_tipo']];
    $response['tipo'] = $sessao['pergunta_tipo'];
    $response['tentativas_restantes'] = 3 - $sessao['tentativas'];
}

$conn->close();
echo json_encode($response);
?>