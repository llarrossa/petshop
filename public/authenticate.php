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

$sql = "SELECT u.*, c.nome as company_name, c.plano, c.subscription_status
        FROM users u
        INNER JOIN companies c ON u.company_id = c.id
        WHERE u.email = :email AND u.status = 'ativo'";

$user = $db->queryOne($sql, [':email' => $email]);

if ($user && password_verify($senha, $user['senha'])) {
    $_SESSION['user_id']             = $user['id'];
    $_SESSION['user_name']           = $user['nome'];
    $_SESSION['user_email']          = $user['email'];
    $_SESSION['company_id']          = $user['company_id'];
    $_SESSION['company_name']        = $user['company_name'];
    $_SESSION['plano']               = $user['plano'];
    $_SESSION['perfil']              = $user['perfil'];
    $_SESSION['subscription_status'] = $user['subscription_status'];

    // Assinatura ativa → dashboard; caso contrário → planos
    if (assinaturaAtiva()) {
        header('Location: ' . APP_URL . '/index.php?page=dashboard');
    } else {
        header('Location: ' . APP_URL . '/public/planos.php');
    }
    exit;
} else {
    $_SESSION['error'] = 'E-mail ou senha incorretos.';
    header('Location: login.php');
    exit;
}
