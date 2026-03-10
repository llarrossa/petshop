<?php
/**
 * Cadastro de Nova Empresa (Tenant)
 * Cria a empresa e o usuário administrador em uma única etapa.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';

// Usuário já logado → redirecionar para o dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/index.php?page=dashboard');
    exit;
}

$errors   = [];
$success  = false;
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Dados recebidos ---
    $nomeEmpresa    = sanitize($_POST['nome_empresa'] ?? '');
    $cnpj           = sanitize($_POST['cnpj'] ?? '');
    $plano          = sanitize($_POST['plano'] ?? 'completo');
    $nomeAdmin      = sanitize($_POST['nome_admin'] ?? '');
    $email          = sanitize($_POST['email'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $senhaConfirm   = $_POST['senha_confirm'] ?? '';

    // Preservar dados para repopular o formulário em caso de erro
    $formData = compact('nomeEmpresa', 'cnpj', 'plano', 'nomeAdmin', 'email');

    // --- Validações ---
    if (empty($nomeEmpresa)) {
        $errors[] = 'O nome do pet shop é obrigatório.';
    }

    if (empty($nomeAdmin)) {
        $errors[] = 'O nome do responsável é obrigatório.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Informe um e-mail válido.';
    }

    if (strlen($senha) < 8) {
        $errors[] = 'A senha deve ter no mínimo 8 caracteres.';
    }

    if ($senha !== $senhaConfirm) {
        $errors[] = 'As senhas não coincidem.';
    }

    if (!array_key_exists($plano, PLANOS)) {
        $errors[] = 'Plano inválido.';
    }

    // Verificar se e-mail já existe
    if (empty($errors)) {
        $db  = Database::getInstance();
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $existing = $db->queryOne($sql, [':email' => $email]);

        if ($existing) {
            $errors[] = 'Já existe uma conta cadastrada com este e-mail.';
        }
    }

    // --- Criar empresa e usuário ---
    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // 1. Inserir empresa
            $sqlCompany = "INSERT INTO companies (nome, cnpj, plano, status)
                           VALUES (:nome, :cnpj, :plano, 'ativo')";
            $db->execute($sqlCompany, [
                ':nome'  => $nomeEmpresa,
                ':cnpj'  => $cnpj ?: null,
                ':plano' => $plano,
            ]);
            $companyId = $db->lastInsertId();

            // 2. Inserir usuário administrador
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sqlUser   = "INSERT INTO users (company_id, nome, email, senha, perfil, status)
                          VALUES (:company_id, :nome, :email, :senha, 'admin', 'ativo')";
            $db->execute($sqlUser, [
                ':company_id' => $companyId,
                ':nome'       => $nomeAdmin,
                ':email'      => $email,
                ':senha'      => $senhaHash,
            ]);
            $userId = $db->lastInsertId();

            $db->commit();

            // 3. Fazer login automático
            $_SESSION['user_id']      = $userId;
            $_SESSION['user_name']    = $nomeAdmin;
            $_SESSION['user_email']   = $email;
            $_SESSION['company_id']   = $companyId;
            $_SESSION['company_name'] = $nomeEmpresa;
            $_SESSION['plano']        = $plano;
            $_SESSION['perfil']       = 'admin';

            $_SESSION['success'] = 'Bem-vindo ao Pawfy! Sua conta foi criada com sucesso.';
            header('Location: ' . APP_URL . '/index.php?page=dashboard');
            exit;

        } catch (Exception $e) {
            $db->rollback();
            $errors[] = 'Ocorreu um erro ao criar a conta. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta — Pawfy</title>
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
            max-width: 520px;
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

        h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid #E2E8F0;
        }

        h2 span {
            display: inline-block;
            background: #EFF6FF;
            color: #2563EB;
            border-radius: 6px;
            padding: 2px 10px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-left: 8px;
            vertical-align: middle;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .section-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94A3B8;
            margin: 20px 0 10px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        label .opt {
            font-weight: 400;
            color: #94A3B8;
            font-size: 0.78rem;
        }

        .form-control {
            width: 100%;
            padding: 10px 13px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 0.88rem;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* Seletor de plano */
        .planos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 4px;
        }

        .plano-option input[type="radio"] { display: none; }

        .plano-option label {
            display: block;
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            padding: 12px 10px;
            text-align: center;
            cursor: pointer;
            transition: all .18s;
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
            background: #F8FAFC;
        }

        .plano-option label .plan-icon { display: block; font-size: 1.4rem; margin-bottom: 4px; }

        .plano-option input[type="radio"]:checked + label {
            border-color: #2563EB;
            background: #EFF6FF;
            color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        }

        .plano-option label:hover { border-color: #93C5FD; }

        /* Botão */
        .btn-submit {
            width: 100%;
            padding: 13px;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: background .18s, transform .18s, box-shadow .18s;
            letter-spacing: .01em;
        }

        .btn-submit:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,.3);
        }

        /* Alertas */
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        .alert-error {
            background: #FEF2F2;
            border-left: 4px solid #EF4444;
            color: #991B1B;
        }

        .alert-error ul { padding-left: 16px; margin-top: 4px; }

        /* Rodapé do card */
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

        @media (max-width: 480px) {
            .card { padding: 28px 20px; }
            .form-row { grid-template-columns: 1fr; }
            .planos-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="card">

        <div class="logo">
            <a href="<?= APP_URL ?>">🐾 Paw<span>fy</span></a>
            <p>Crie a conta do seu pet shop — é grátis para começar</p>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php if (count($errors) === 1): ?>
                <?= $errors[0] ?>
            <?php else: ?>
                <strong>Corrija os erros abaixo:</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                    <li><?= $e ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">

            <!-- Seção: Pet Shop -->
            <p class="section-label">Seu Pet Shop</p>

            <div class="form-group">
                <label for="nome_empresa">Nome do Pet Shop</label>
                <input type="text" id="nome_empresa" name="nome_empresa"
                       class="form-control" required
                       placeholder="Ex.: Pet Shop da Ana"
                       value="<?= htmlspecialchars($formData['nomeEmpresa'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cnpj">CNPJ <span class="opt">(opcional)</span></label>
                    <input type="text" id="cnpj" name="cnpj"
                           class="form-control"
                           placeholder="00.000.000/0001-00"
                           maxlength="18"
                           value="<?= htmlspecialchars($formData['cnpj'] ?? '') ?>">
                </div>
            </div>

            <!-- Plano -->
            <p class="section-label">Plano</p>

            <div class="planos-grid">
                <div class="plano-option">
                    <input type="radio" id="plano_banho" name="plano" value="banho_tosa"
                           <?= (($formData['plano'] ?? 'completo') === 'banho_tosa') ? 'checked' : '' ?>>
                    <label for="plano_banho">
                        <span class="plan-icon">✂️</span>
                        Banho & Tosa
                    </label>
                </div>
                <div class="plano-option">
                    <input type="radio" id="plano_loja" name="plano" value="loja"
                           <?= (($formData['plano'] ?? 'completo') === 'loja') ? 'checked' : '' ?>>
                    <label for="plano_loja">
                        <span class="plan-icon">🏪</span>
                        Pet Shop
                    </label>
                </div>
                <div class="plano-option">
                    <input type="radio" id="plano_completo" name="plano" value="completo"
                           <?= (($formData['plano'] ?? 'completo') === 'completo') ? 'checked' : '' ?>>
                    <label for="plano_completo">
                        <span class="plan-icon">🐾</span>
                        Completo
                    </label>
                </div>
            </div>

            <!-- Seção: Dados do responsável -->
            <p class="section-label">Responsável pela conta</p>

            <div class="form-group">
                <label for="nome_admin">Nome completo</label>
                <input type="text" id="nome_admin" name="nome_admin"
                       class="form-control" required
                       placeholder="Seu nome completo"
                       value="<?= htmlspecialchars($formData['nomeAdmin'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email">E-mail de acesso</label>
                <input type="email" id="email" name="email"
                       class="form-control" required
                       placeholder="voce@seumail.com"
                       value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha"
                           class="form-control" required
                           placeholder="Mínimo 8 caracteres">
                </div>
                <div class="form-group">
                    <label for="senha_confirm">Confirmar senha</label>
                    <input type="password" id="senha_confirm" name="senha_confirm"
                           class="form-control" required
                           placeholder="Repita a senha">
                </div>
            </div>

            <button type="submit" class="btn-submit">Criar minha conta grátis →</button>

        </form>

        <div class="card-footer">
            Já tem uma conta? <a href="login.php">Entrar</a>
        </div>

    </div>

    <script>
        // Máscara CNPJ
        document.getElementById('cnpj').addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '');
            if (v.length > 14) v = v.slice(0, 14);
            v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d)/, '$1-$2');
            this.value = v;
        });
    </script>
</body>
</html>
