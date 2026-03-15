<?php
/**
 * Logout do Sistema
 */

require_once __DIR__ . '/../config/config.php';

// Limpar superglobal e destruir sessão completamente
$_SESSION = [];

// Expirar o cookie de sessão no navegador
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

header('Location: ' . APP_URL . '/public/login.php');
exit;
