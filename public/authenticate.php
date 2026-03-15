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

// Rate limiting: máximo 5 tentativas por IP a cada 15 minutos
$_rl_key = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0') . ':login');
if (!checkRateLimit($_rl_key, 'login', 5, 900)) {
    $_SESSION['error'] = 'Muitas tentativas de login. Aguarde 15 minutos e tente novamente.';
    header('Location: login.php');
    exit;
}

$email = sanitize($_POST['email']);
$senha = $_POST['senha'];

$db = Database::getInstance();

$sql = "SELECT u.*, c.nome as company_name, c.plano, c.subscription_status, c.trial_ends_at
        FROM users u
        INNER JOIN companies c ON u.company_id = c.id
        WHERE u.email = :email AND u.status = 'ativo'";

$user = $db->queryOne($sql, [':email' => $email]);

if ($user && password_verify($senha, $user['senha'])) {
    // Bloquear login se e-mail ainda não foi confirmado
    if (empty($user['email_verified'])) {
        $_SESSION['cadastro_email'] = $email;
        $_SESSION['error'] = 'Confirme seu e-mail antes de entrar. Verifique sua caixa de entrada.';
        header('Location: email_pendente.php');
        exit;
    }

    // Login bem-sucedido: regenerar ID de sessão para evitar session fixation
    session_regenerate_id(true);
    // Limpar tentativas de login acumuladas
    resetRateLimit($_rl_key, 'login');

    $subscriptionStatus = $user['subscription_status'];
    $trialEndsAt        = $user['trial_ends_at'];

    // Verificar se o trial expirou
    if ($subscriptionStatus === 'trialing' && !empty($trialEndsAt) && strtotime($trialEndsAt) < time()) {
        $db->execute(
            "UPDATE companies SET subscription_status = 'incomplete' WHERE id = :id",
            [':id' => $user['company_id']]
        );
        $subscriptionStatus = 'incomplete';
        $trialEndsAt        = null;
    }

    $_SESSION['user_id']             = $user['id'];
    $_SESSION['user_name']           = $user['nome'];
    $_SESSION['user_email']          = $user['email'];
    $_SESSION['company_id']          = $user['company_id'];
    $_SESSION['company_name']        = $user['company_name'];
    $_SESSION['plano']               = $user['plano'];
    $_SESSION['perfil']              = $user['perfil'];
    $_SESSION['subscription_status'] = $subscriptionStatus;
    $_SESSION['trial_ends_at']       = $trialEndsAt;

    // Assinatura ativa (ou trial válido) → dashboard; caso contrário → planos
    if (assinaturaAtiva()) {
        header('Location: ' . APP_URL . '/index.php?page=dashboard');
    } else {
        $suffix = ($subscriptionStatus === 'incomplete' && empty($user['stripe_subscription_id'])) ? '?trial_expirado=1' : '';
        header('Location: ' . APP_URL . '/public/planos.php' . $suffix);
    }
    exit;
} else {
    $_SESSION['error'] = 'E-mail ou senha incorretos.';
    header('Location: login.php');
    exit;
}
