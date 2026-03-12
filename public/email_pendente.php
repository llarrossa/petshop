<?php
/**
 * Página intermediária pós-cadastro
 * Informa ao usuário que um e-mail de confirmação foi enviado.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';
require_once __DIR__ . '/../helpers/mailer.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/index.php?page=dashboard');
    exit;
}

$email = $_SESSION['cadastro_email'] ?? null;

// Se acessar sem ter acabado de se cadastrar, redireciona
if (!$email) {
    header('Location: ' . APP_URL . '/public/register.php');
    exit;
}

$mensagemReenvio = '';
$erroReenvio     = '';

// Reenvio do e-mail de confirmação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'reenviar') {
    $db   = Database::getInstance();
    $user = $db->queryOne(
        "SELECT id, nome FROM users WHERE email = :email AND email_verified = 0 LIMIT 1",
        [':email' => $email]
    );

    if ($user) {
        // Invalidar tokens anteriores
        $db->execute(
            "UPDATE email_verifications SET used = 1 WHERE user_id = :uid",
            [':uid' => $user['id']]
        );

        // Novo token (válido por 24h)
        $token     = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 86400);
        $db->execute(
            "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (:uid, :token, :exp)",
            [':uid' => $user['id'], ':token' => $token, ':exp' => $expiresAt]
        );

        $verifyUrl   = APP_URL . '/public/verify_email.php?token=' . $token;
        $nomeEsc     = htmlspecialchars($user['nome']);
        $html = "
        <div style='font-family:sans-serif;max-width:500px;margin:0 auto;color:#1E293B'>
            <h2 style='color:#2563EB'>🐾 Confirme seu e-mail — Pawfy</h2>
            <p>Olá, <strong>{$nomeEsc}</strong>!</p>
            <p>Você solicitou o reenvio do link de confirmação. Clique no botão abaixo para ativar sua conta:</p>
            <p style='margin:24px 0'>
                <a href='{$verifyUrl}'
                   style='background:#2563EB;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700'>
                    Confirmar meu e-mail
                </a>
            </p>
            <p style='font-size:0.85rem;color:#64748B'>O link é válido por <strong>24 horas</strong>.</p>
            <hr style='border:none;border-top:1px solid #E2E8F0;margin:24px 0'>
            <p style='font-size:0.75rem;color:#94A3B8'>Pawfy — Sistema de gestão para pet shops</p>
        </div>";

        $ok = enviarEmailSMTP($email, $user['nome'], 'Confirme seu e-mail — Pawfy 🐾', $html);
        $mensagemReenvio = $ok
            ? 'E-mail reenviado com sucesso! Verifique sua caixa de entrada.'
            : 'Não foi possível reenviar. Tente novamente em instantes.';
        if (!$ok) $erroReenvio = $mensagemReenvio;
    } else {
        $mensagemReenvio = 'Não foi possível reenviar. A conta pode já ter sido confirmada.';
        $erroReenvio     = $mensagemReenvio;
    }
}

// Mascarar e-mail para exibição: lu***@gmail.com
function mascararEmail(string $email): string {
    [$local, $dominio] = explode('@', $email);
    $visivelLocal = substr($local, 0, min(2, strlen($local)));
    return $visivelLocal . str_repeat('*', max(1, strlen($local) - 2)) . '@' . $dominio;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifique seu e-mail — Pawfy</title>
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
            max-width: 440px;
            padding: 40px 44px;
            text-align: center;
        }
        .icon { font-size: 3rem; margin-bottom: 16px; }
        h1 { font-size: 1.4rem; font-weight: 800; color: #1E293B; margin-bottom: 12px; }
        .desc { font-size: 0.9rem; color: #64748B; line-height: 1.6; margin-bottom: 24px; }
        .desc strong { color: #1E293B; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 16px; font-size: 0.85rem; text-align: left; }
        .alert-success { background: #F0FDF4; border-left: 4px solid #22C55E; color: #166534; }
        .alert-error   { background: #FEF2F2; border-left: 4px solid #EF4444; color: #991B1B; }
        .steps {
            background: #F8FAFC;
            border-radius: 10px;
            padding: 16px 20px;
            text-align: left;
            margin-bottom: 24px;
        }
        .steps p { font-size: 0.82rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px; }
        .steps ol { padding-left: 18px; color: #475569; font-size: 0.88rem; line-height: 1.8; }
        .btn-reenviar {
            width: 100%; padding: 12px; background: #2563EB; color: #fff; border: none;
            border-radius: 10px; font-size: 0.95rem; font-weight: 700; cursor: pointer;
            transition: background .18s; margin-bottom: 12px;
        }
        .btn-reenviar:hover { background: #1D4ED8; }
        .link-login { font-size: 0.85rem; color: #64748B; }
        .link-login a { color: #2563EB; font-weight: 600; text-decoration: none; }
        .link-login a:hover { text-decoration: underline; }
        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 0.82rem; color: rgba(255,255,255,.7); text-decoration: none; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">📬</div>
        <h1>Verifique seu e-mail</h1>

        <p class="desc">
            Enviamos um link de confirmação para<br>
            <strong><?= mascararEmail($email) ?></strong>
        </p>

        <?php if ($mensagemReenvio && !$erroReenvio): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagemReenvio) ?></div>
        <?php elseif ($erroReenvio): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erroReenvio) ?></div>
        <?php endif; ?>

        <div class="steps">
            <p>O que fazer agora</p>
            <ol>
                <li>Abra seu e-mail</li>
                <li>Procure a mensagem de <strong>Pawfy</strong></li>
                <li>Clique em <strong>"Confirmar meu e-mail"</strong></li>
                <li>Você será redirecionado para o sistema</li>
            </ol>
        </div>

        <form method="POST">
            <input type="hidden" name="acao" value="reenviar">
            <button type="submit" class="btn-reenviar">Reenviar e-mail de confirmação</button>
        </form>

        <p class="link-login">
            Já confirmou? <a href="login.php">Entrar na conta</a>
        </p>
    </div>

    <a href="<?= APP_URL ?>" class="back-link">← Voltar para o site</a>
</body>
</html>
