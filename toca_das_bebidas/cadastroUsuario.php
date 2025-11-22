<?php
session_start(); 

header('Content-Type: application/json');
include("conexao.php");

$response = [
    'success' => false,
    'message' => 'Ocorreu um erro desconhecido.'
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email           = trim($_POST['email']);
    $nome            = trim($_POST['nome']);
    $data_nascimento = trim($_POST['data_nascimento']);
    $sexo            = trim($_POST['sexo']);
    $nome_materno    = trim($_POST['nome_materno']);
    $cpf             = trim($_POST['cpf']);
    $celular         = trim($_POST['celular']);
    $telefone_fixo   = trim($_POST['telefone_fixo']);
    $endereco        = trim($_POST['endereco']);
    $login           = trim($_POST['login']);
    $senha           = trim($_POST['senha_hash']);
    $confirmar_senha = trim($_POST['confirmar_senha']);

    if (
        empty($email) || empty($nome) || empty($data_nascimento) || empty($sexo) ||
        empty($nome_materno) || empty($cpf) || empty($celular) || empty($endereco) ||
        empty($login) || empty($senha)
    ) {
        $response['message'] = 'Preencha todos os campos obrigatórios!';
        echo json_encode($response);
        exit;
    }

    if ($senha !== $confirmar_senha) {
        $response['message'] = 'As senhas não coincidem!'; 
        echo json_encode($response);
        exit;
    }

    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? OR cpf = ? OR login = ?");
    $check->bind_param("sss", $email, $cpf, $login);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $response['message'] = 'Já existe um usuário com esse e-mail, CPF ou login!';
        $check->close();
        echo json_encode($response);
        exit;
    }
    $check->close();

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios 
        (email, nome, data_nascimento, sexo, nome_materno, cpf, celular, telefone_fixo, endereco, login, senha_hash) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        $response['message'] = 'Erro na preparação da query: ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param(
        "sssssssssss",
        $email, $nome, $data_nascimento, $sexo, $nome_materno,
        $cpf, $celular, $telefone_fixo, $endereco, $login, $senha_hash
    );

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Usuário cadastrado com sucesso!';
        
        $_SESSION['usuario_id'] = $stmt->insert_id; 
        $_SESSION['usuario_login'] = $login;
        $_SESSION['usuario_nome'] = $nome;

    } else {
        $response['message'] = 'Erro ao cadastrar: ' . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

echo json_encode($response);
?>