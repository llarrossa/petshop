<?php
require_once __DIR__ . '/../config/config.php';

// Usuário já logado → redirecionar para o dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/index.php?page=dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — Pawfy</title>
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

        .logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo a {
            text-decoration: none;
            font-size: 1.8rem;
            font-weight: 800;
            color: #2563EB;
            letter-spacing: -.02em;
        }

        .logo a span { color: #F97316; }

        .logo p {
            color: #64748B;
            font-size: 0.88rem;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #1E293B;
            background: #FAFAFA;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
            background: #fff;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 4px;
            transition: background .18s, transform .18s, box-shadow .18s;
            letter-spacing: .01em;
        }

        .btn-login:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,.3);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 0.85rem;
        }

        .alert-error {
            background: #FEF2F2;
            border-left: 4px solid #EF4444;
            color: #991B1B;
        }

        .card-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #E2E8F0;
            font-size: 0.85rem;
            color: #64748B;
        }

        .card-footer a {
            color: #2563EB;
            font-weight: 600;
            text-decoration: none;
        }

        .card-footer a:hover { text-decoration: underline; }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.82rem;
            color: rgba(255,255,255,.7);
            text-decoration: none;
            transition: color .15s;
        }

        .back-link:hover { color: #fff; }

        @media (max-width: 480px) {
            .card { padding: 28px 20px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <a href="<?= APP_URL ?>">🐾 Paw<span>fy</span></a>
            <p>Acesse o painel do seu pet shop</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="authenticate.php">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email"
                       class="form-control" required autofocus
                       placeholder="voce@seumail.com">
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha"
                       class="form-control" required
                       placeholder="Sua senha">
            </div>

            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div style="text-align:right;margin-top:10px">
            <a href="forgot_password.php" style="font-size:0.82rem;color:#2563EB;text-decoration:none">
                Esqueci minha senha
            </a>
        </div>

        <div class="card-footer">
            Não tem conta ainda? <a href="register.php">Criar conta grátis</a>
        </div>
    </div>

    <a href="<?= APP_URL ?>" class="back-link">← Voltar para o site</a>
</body>
</html>
