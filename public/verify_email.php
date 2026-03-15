<?php
/**
 * Confirmação de e-mail via token
 * Valida o token, marca o usuário como verificado e faz auto-login.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/index.php?page=dashboard');
    exit;
}

$db    = Database::getInstance();
$token = trim($_GET['token'] ?? '');

$registro = null;
if ($token) {
    $registro = $db->queryOne(
        "SELECT ev.*, u.id as user_id, u.nome, u.email, u.perfil, u.email_verified,
                c.id as company_id, c.nome as company_name, c.plano,
                c.subscription_status, c.trial_ends_at
           FROM email_verifications ev
           JOIN users u ON ev.user_id = u.id
           JOIN companies c ON u.company_id = c.id
          WHERE ev.token = :token
            AND ev.used = 0
            AND ev.expires_at > NOW()
          LIMIT 1",
        [':token' => $token]
    );
}

if (!$registro) {
    $erro = true;
} elseif ($registro['email_verified']) {
    // Já confirmado anteriormente — apenas loga
    $erro = false;
    $jaConfirmado = true;
} else {
    // Marcar como verificado e remover token (não reutilizável)
    $db->execute(
        "UPDATE users SET email_verified = 1 WHERE id = :id",
        [':id' => $registro['user_id']]
    );
    $db->execute(
        "DELETE FROM email_verifications WHERE token = :token",
        [':token' => $token]
    );
    $erro         = false;
    $jaConfirmado = false;
}

if (!$erro) {
    // Regenerar ID de sessão antes do login automático (evita session fixation)
    session_regenerate_id(true);

    // Fazer login automático
    $subscriptionStatus = $registro['subscription_status'];
    $trialEndsAt        = $registro['trial_ends_at'];

    if ($subscriptionStatus === 'trialing' && !empty($trialEndsAt) && strtotime($trialEndsAt) < time()) {
        $db->execute(
            "UPDATE companies SET subscription_status = 'incomplete' WHERE id = :id",
            [':id' => $registro['company_id']]
        );
        $subscriptionStatus = 'incomplete';
        $trialEndsAt        = null;
    }

    $_SESSION['user_id']             = $registro['user_id'];
    $_SESSION['user_name']           = $registro['nome'];
    $_SESSION['user_email']          = $registro['email'];
    $_SESSION['company_id']          = $registro['company_id'];
    $_SESSION['company_name']        = $registro['company_name'];
    $_SESSION['plano']               = $registro['plano'];
    $_SESSION['perfil']              = $registro['perfil'];
    $_SESSION['subscription_status'] = $subscriptionStatus;
    $_SESSION['trial_ends_at']       = $trialEndsAt;
    unset($_SESSION['cadastro_email']);

    $suffix = $jaConfirmado ? '' : '&conta_confirmada=1';

    if (assinaturaAtiva()) {
        header('Location: ' . APP_URL . '/index.php?page=dashboard' . $suffix);
    } else {
        header('Location: ' . APP_URL . '/public/planos.php?trial_expirado=1');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link inválido — Pawfy</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 55%, #1D4ED8 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 24px 64px rgba(0,0,0,.25);
            width: 100%;
            max-width: 400px;
            padding: 40px 44px;
            text-align: center;
        }
        .icon { font-size: 2.5rem; margin-bottom: 16px; }
        h1 { font-size: 1.3rem; font-weight: 800; color: #1E293B; margin-bottom: 10px; }
        p  { font-size: 0.88rem; color: #64748B; line-height: 1.6; margin-bottom: 24px; }
        .btn {
            display: inline-block; padding: 11px 24px; background: #2563EB; color: #fff;
            border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 0.9rem;
            transition: background .18s;
        }
        .btn:hover { background: #1D4ED8; }
        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 0.82rem; color: rgba(255,255,255,.7); text-decoration: none; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚠️</div>
        <h1>Link inválido ou expirado</h1>
        <p>
            Este link de confirmação não é mais válido.<br>
            Pode ter expirado (válido por 24h) ou já ter sido utilizado.
        </p>
        <a href="login.php" class="btn">Ir para o login</a>
    </div>
    <a href="<?= APP_URL ?>" class="back-link">← Voltar para o site</a>
</body>
</html>
