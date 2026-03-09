<?php
/**
 * Autenticação de Usuário
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = sanitize($_POST['email']);
$senha = $_POST['senha'];

$db = Database::getInstance();

// Buscar usuário
$sql = "SELECT u.*, c.nome as company_name, c.plano
        FROM users u
        INNER JOIN companies c ON u.company_id = c.id
        WHERE u.email = :email AND u.status = 'ativo'";

$user = $db->queryOne($sql, [':email' => $email]);

if ($user && password_verify($senha, $user['senha'])) {
    // Login bem-sucedido
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nome'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['company_id'] = $user['company_id'];
    $_SESSION['company_name'] = $user['company_name'];
    $_SESSION['plano'] = $user['plano'];
    $_SESSION['perfil'] = $user['perfil'];

    header('Location: ../index.php?page=dashboard');
    exit;
} else {
    // Login falhou
    $_SESSION['error'] = 'E-mail ou senha incorretos.';
    header('Location: login.php');
    exit;
}
