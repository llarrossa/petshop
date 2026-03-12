<?php
/**
 * Redefinição de senha via token
 * GET:  valida token e exibe o formulário
 * POST: valida, altera a senha e invalida o token
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/index.php?page=dashboard');
    exit;
}

$db     = Database::getInstance();
$token  = trim($_GET['token'] ?? '');
$errors = [];
$sucesso = false;

// Validar token
$registro = null;
if ($token) {
    $registro = $db->queryOne(
        "SELECT * FROM password_resets
          WHERE token = :token
            AND used = 0
            AND expires_at > NOW()
          LIMIT 1",
        [':token' => $token]
    );
}

$tokenInvalido = empty($registro);

if (!$tokenInvalido && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaSenha      = $_POST['nova_senha']      ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if (strlen($novaSenha) < 8) {
        $errors[] = 'A senha deve ter no mínimo 8 caracteres.';
    }
    if ($novaSenha !== $confirmarSenha) {
        $errors[] = 'As senhas não coincidem.';
    }

    if (empty($errors)) {
        $user = $db->queryOne(
            "SELECT id FROM users WHERE email = :email LIMIT 1",
            [':email' => $registro['email']]
        );

        if ($user) {
            $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $db->execute(
                "UPDATE users SET senha = :senha WHERE id = :id",
                [':senha' => $hash, ':id' => $user['id']]
            );
            $db->execute(
                "UPDATE password_resets SET used = 1 WHERE token = :token",
                [':token' => $token]
            );
            $sucesso = true;
        } else {
            $errors[] = 'Usuário não encontrado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha — Pawfy</title>
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
        small { color: #94A3B8; font-weight: 400; font-size: 0.78rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <a href="<?= APP_URL ?>">🐾 Paw<span>fy</span></a>
            <p>Redefinir senha</p>
        </div>

        <?php if ($tokenInvalido): ?>
        <div class="alert alert-error">
            Este link é inválido ou já expirou. Solicite um novo link de recuperação.
        </div>
        <div class="card-footer">
            <a href="forgot_password.php">Solicitar novo link</a> &nbsp;·&nbsp;
            <a href="login.php">Voltar ao login</a>
        </div>

        <?php elseif ($sucesso): ?>
        <div class="alert alert-success">
            Senha redefinida com sucesso! Agora você pode entrar com a nova senha.
        </div>
        <div class="card-footer">
            <a href="login.php">← Ir para o login</a>
        </div>

        <?php else: ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul style="padding-left:16px;margin:0">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="?token=<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <label for="nova_senha">Nova senha <small>(mínimo 8 caracteres)</small></label>
                <input type="password" id="nova_senha" name="nova_senha"
                       class="form-control" required minlength="8" autofocus>
            </div>
            <div class="form-group">
                <label for="confirmar_senha">Confirmar nova senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha"
                       class="form-control" required minlength="8">
            </div>
            <button type="submit" class="btn-submit">Redefinir senha</button>
        </form>

        <div class="card-footer">
            <a href="login.php">← Voltar ao login</a>
        </div>
        <?php endif; ?>
    </div>

    <a href="<?= APP_URL ?>" class="back-link">← Voltar para o site</a>
</body>
</html>
