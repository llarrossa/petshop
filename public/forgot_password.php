<?php
/**
 * Esqueci minha senha
 * Gera token, salva em password_resets e envia link por e-mail.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';
require_once __DIR__ . '/../helpers/mailer.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/index.php?page=dashboard');
    exit;
}

$enviado = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Informe um e-mail válido.';
    } else {
        $db   = Database::getInstance();
        $user = $db->queryOne(
            "SELECT id, nome FROM users WHERE email = :email LIMIT 1",
            [':email' => $email]
        );

        // Sempre exibe mensagem de sucesso para não revelar cadastros
        if ($user) {
            // Invalidar tokens anteriores deste e-mail
            $db->execute(
                "UPDATE password_resets SET used = 1 WHERE email = :email",
                [':email' => $email]
            );

            // Gerar token seguro (64 hex chars = 32 bytes)
            $token     = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hora

            $db->execute(
                "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)",
                [':email' => $email, ':token' => $token, ':expires_at' => $expiresAt]
            );

            $resetLink = APP_URL . '/public/reset_password.php?token=' . $token;
            $nome      = htmlspecialchars($user['nome']);

            $html = "
            <div style='font-family:sans-serif;max-width:500px;margin:0 auto;color:#1E293B'>
                <h2 style='color:#2563EB'>🐾 Pawfy — Redefinição de senha</h2>
                <p>Olá, <strong>{$nome}</strong>!</p>
                <p>Recebemos uma solicitação para redefinir a senha da sua conta.</p>
                <p style='margin:24px 0'>
                    <a href='{$resetLink}'
                       style='background:#2563EB;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700'>
                        Redefinir minha senha
                    </a>
                </p>
                <p style='font-size:0.85rem;color:#64748B'>
                    O link é válido por <strong>1 hora</strong>.<br>
                    Se não foi você quem solicitou, ignore este e-mail.
                </p>
                <hr style='border:none;border-top:1px solid #E2E8F0;margin:24px 0'>
                <p style='font-size:0.75rem;color:#94A3B8'>Pawfy — Sistema de gestão para pet shops</p>
            </div>";

            enviarEmailSMTP($email, $user['nome'], 'Redefinição de senha — Pawfy', $html);
        }

        $enviado = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci minha senha — Pawfy</title>
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
        }
        .logo { text-align: center; margin-bottom: 28px; }
        .logo a { text-decoration: none; font-size: 1.8rem; font-weight: 800; color: #2563EB; letter-spacing: -.02em; }
        .logo a span { color: #F97316; }
        .logo p { color: #64748B; font-size: 0.88rem; margin-top: 4px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .form-control {
            width: 100%; padding: 11px 13px; border: 1px solid #D1D5DB;
            border-radius: 8px; font-size: 0.9rem; color: #1E293B;
            background: #FAFAFA; transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus { outline: none; border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,.12); background: #fff; }
        .btn-submit {
            width: 100%; padding: 13px; background: #2563EB; color: #fff; border: none;
            border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer;
            margin-top: 4px; transition: background .18s;
        }
        .btn-submit:hover { background: #1D4ED8; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 18px; font-size: 0.85rem; }
        .alert-error   { background: #FEF2F2; border-left: 4px solid #EF4444; color: #991B1B; }
        .alert-success { background: #F0FDF4; border-left: 4px solid #22C55E; color: #166534; }
        .card-footer { text-align: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid #E2E8F0; font-size: 0.85rem; color: #64748B; }
        .card-footer a { color: #2563EB; font-weight: 600; text-decoration: none; }
        .card-footer a:hover { text-decoration: underline; }
        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 0.82rem; color: rgba(255,255,255,.7); text-decoration: none; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <a href="<?= APP_URL ?>">
                <img src="<?= APP_URL ?>/public/img/primary-logo.png" alt="Pawfy" style="max-height:48px;max-width:180px;">
            </a>
            <p>Recuperação de senha</p>
        </div>

        <?php if ($enviado): ?>
        <div class="alert alert-success">
            Se este e-mail estiver cadastrado, você receberá as instruções em instantes.
            Verifique também a caixa de spam.
        </div>
        <div class="card-footer">
            <a href="login.php">← Voltar para o login</a>
        </div>

        <?php else: ?>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <p style="font-size:0.88rem;color:#64748B;margin-bottom:20px">
            Informe seu e-mail cadastrado e enviaremos um link para redefinir sua senha.
        </p>

        <form method="POST">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-control"
                       required autofocus placeholder="voce@seumail.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <button type="submit" class="btn-submit">Enviar link de recuperação</button>
        </form>

        <div class="card-footer">
            Lembrou a senha? <a href="login.php">Entrar</a>
        </div>
        <?php endif; ?>
    </div>

    <a href="<?= APP_URL ?>" class="back-link">← Voltar para o site</a>
</body>
</html>
