<?php
/**
 * Seleção e gerenciamento de plano / assinatura Stripe
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';

verificarLogin();

$isNovo    = isset($_GET['novo']);
$isCanceled = isset($_GET['canceled']);
$status    = $_SESSION['subscription_status'] ?? 'incomplete';
$planoAtual = $_SESSION['plano'] ?? 'completo';

$db      = Database::getInstance();
$company = $db->queryOne("SELECT * FROM companies WHERE id = :id", [':id' => getCompanyId()]);

// Atualizar status da sessão com o banco (garante dados frescos)
if ($company) {
    $_SESSION['subscription_status'] = $company['subscription_status'];
    $_SESSION['plano']               = $company['plano'];
    $status    = $company['subscription_status'];
    $planoAtual = $company['plano'];
}

$jaAssinado = in_array($status, SUBSCRIPTION_ACTIVE_STATUSES);

$plansInfo = [
    'banho_tosa' => [
        'icon'     => '✂️',
        'nome'     => 'Banho & Tosa',
        'desc'     => 'Para pet shops focados em serviços de estética e atendimento.',
        'features' => ['Agenda de atendimentos', 'Cadastro de tutores e pets', 'Serviços e profissionais', 'Dashboard gerencial'],
        'color'    => '#2563EB',
        'bg'       => '#EFF6FF',
    ],
    'loja' => [
        'icon'     => '🏪',
        'nome'     => 'Pet Shop',
        'desc'     => 'Para lojas focadas em vendas de produtos e controle de estoque.',
        'features' => ['Cadastro de produtos', 'PDV — Ponto de Venda', 'Controle de estoque', 'Controle financeiro', 'Relatórios de vendas', 'Dashboard gerencial'],
        'color'    => '#F97316',
        'bg'       => '#FFF7ED',
        'popular'  => true,
    ],
    'completo' => [
        'icon'     => '🐾',
        'nome'     => 'Completo',
        'desc'     => 'Para pet shops completos com serviços e venda de produtos.',
        'features' => ['Todos os módulos', 'Agenda + PDV integrados', 'Prontuário veterinário', 'Estoque e financeiro', 'Relatórios completos'],
        'color'    => '#2563EB',
        'bg'       => '#EFF6FF',
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $jaAssinado ? 'Meu Plano' : 'Escolha seu plano' ?> — Pawfy</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #F8FAFC;
            color: #1E293B;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-logo {
            text-decoration: none;
            font-size: 1.35rem;
            font-weight: 800;
            color: #2563EB;
            letter-spacing: -.02em;
        }

        .topbar-logo span { color: #F97316; }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.85rem;
            color: #64748B;
        }

        .topbar-user a {
            color: #EF4444;
            text-decoration: none;
            font-weight: 600;
        }

        .topbar-user a:hover { text-decoration: underline; }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 55%, #1D4ED8 100%);
            color: #fff;
            text-align: center;
            padding: 48px 24px 56px;
        }

        .hero h1 {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hero p { opacity: .85; font-size: 1rem; }

        /* ── ALERT ── */
        .alert-wrap { max-width: 960px; margin: 24px auto 0; padding: 0 24px; }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-info    { background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; }
        .alert-warning { background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; }
        .alert-success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; }

        /* ── PLANS GRID ── */
        .plans-wrap { max-width: 960px; margin: 32px auto; padding: 0 24px; }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            align-items: start;
        }

        .plan-card {
            background: #fff;
            border: 2px solid #E2E8F0;
            border-radius: 18px;
            padding: 32px 28px;
            position: relative;
            transition: box-shadow .2s, border-color .2s;
        }

        .plan-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.09); }

        .plan-card.popular { border-color: #F97316; box-shadow: 0 4px 24px rgba(249,115,22,.15); }

        .plan-card.current { border-color: #2563EB; box-shadow: 0 4px 24px rgba(37,99,235,.15); }

        .badge-popular {
            position: absolute; top: -14px; left: 50%; transform: translateX(-50%);
            background: #F97316; color: #fff;
            font-size: 0.68rem; font-weight: 800;
            padding: 3px 14px; border-radius: 999px;
            letter-spacing: .07em; text-transform: uppercase;
            white-space: nowrap;
        }

        .badge-current {
            position: absolute; top: -14px; left: 50%; transform: translateX(-50%);
            background: #2563EB; color: #fff;
            font-size: 0.68rem; font-weight: 800;
            padding: 3px 14px; border-radius: 999px;
            letter-spacing: .07em; text-transform: uppercase;
            white-space: nowrap;
        }

        .plan-icon { font-size: 2rem; display: block; margin-bottom: 14px; }

        .plan-name { font-size: 1.15rem; font-weight: 800; margin-bottom: 8px; }

        .plan-desc { color: #64748B; font-size: 0.83rem; line-height: 1.5; margin-bottom: 20px; min-height: 48px; }

        .plan-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 24px;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.83rem;
            color: #374151;
        }

        .plan-check {
            width: 18px; height: 18px;
            border-radius: 50%;
            background: #D1FAE5; color: #059669;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; font-weight: 800;
            flex-shrink: 0;
        }

        .plan-card.popular .plan-check { background: #FFEDD5; color: #EA580C; }

        /* Botões de assinatura */
        .btn-subscribe {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .18s;
            letter-spacing: .01em;
        }

        .btn-subscribe-primary {
            background: #2563EB;
            color: #fff;
        }

        .btn-subscribe-primary:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,.3);
        }

        .btn-subscribe-orange {
            background: #F97316;
            color: #fff;
        }

        .btn-subscribe-orange:hover {
            background: #EA6C0A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249,115,22,.3);
        }

        .btn-subscribe-outline {
            background: transparent;
            color: #2563EB;
            border: 2px solid #2563EB;
        }

        .btn-subscribe-outline:hover { background: #EFF6FF; }

        .btn-current {
            width: 100%;
            padding: 12px;
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            background: #F8FAFC;
            color: #64748B;
            cursor: default;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* Link para o dashboard */
        .dashboard-link-wrap {
            text-align: center;
            margin-top: 36px;
            padding-bottom: 48px;
        }

        .dashboard-link-wrap a {
            display: inline-block;
            padding: 13px 32px;
            background: #1E293B;
            color: #fff;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: background .18s;
        }

        .dashboard-link-wrap a:hover { background: #0F172A; }

        /* Segurança */
        .security-note {
            text-align: center;
            font-size: 0.78rem;
            color: #94A3B8;
            margin-top: 20px;
            padding-bottom: 8px;
        }

        @media (max-width: 820px) {
            .plans-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto; }
        }

        @media (max-width: 480px) {
            .topbar { padding: 0 16px; }
            .plans-wrap { padding: 0 16px; }
        }
    </style>
</head>
<body>

    <!-- Topbar -->
    <header class="topbar">
        <a href="<?= APP_URL ?>" class="topbar-logo">🐾 Paw<span>fy</span></a>
        <div class="topbar-user">
            <span>Olá, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></strong></span>
            <a href="<?= APP_URL ?>/public/logout.php">Sair</a>
        </div>
    </header>

    <!-- Hero -->
    <div class="hero">
        <?php if ($jaAssinado): ?>
            <h1>Seu plano atual</h1>
            <p><?= htmlspecialchars($_SESSION['company_name'] ?? '') ?> · Assinatura ativa</p>
        <?php elseif ($isNovo): ?>
            <h1>Bem-vindo ao Pawfy! 🎉</h1>
            <p>Sua conta foi criada. Escolha um plano para começar a usar o sistema.</p>
        <?php else: ?>
            <h1>Escolha seu plano</h1>
            <p>Assine para ter acesso completo ao sistema de gestão do seu pet shop.</p>
        <?php endif; ?>
    </div>

    <!-- Alertas -->
    <div class="alert-wrap">
        <?php if ($isCanceled): ?>
        <div class="alert alert-warning">
            ⚠️ A assinatura não foi concluída. Escolha um plano para continuar.
        </div>
        <?php elseif ($isNovo && !$jaAssinado): ?>
        <div class="alert alert-info">
            ℹ️ Conta criada! Selecione um plano abaixo para ativar seu acesso ao Pawfy.
        </div>
        <?php elseif ($status === 'past_due'): ?>
        <div class="alert alert-warning">
            ⚠️ Sua assinatura está com pagamento pendente. Atualize seu método de pagamento para continuar usando o sistema.
        </div>
        <?php elseif ($jaAssinado): ?>
        <div class="alert alert-success">
            ✓ Assinatura ativa. Você tem acesso completo ao plano <strong><?= htmlspecialchars($plansInfo[$planoAtual]['nome']) ?></strong>.
        </div>
        <?php endif; ?>
    </div>

    <!-- Cards de plano -->
    <div class="plans-wrap">
        <div class="plans-grid">
            <?php foreach ($plansInfo as $chave => $info):
                $isCurrent = ($jaAssinado && $chave === $planoAtual);
                $isPopular  = !empty($info['popular']);
                $cardClass  = $isCurrent ? 'current' : ($isPopular ? 'popular' : '');
            ?>
            <div class="plan-card <?= $cardClass ?>">
                <?php if ($isCurrent): ?>
                    <div class="badge-current">Plano atual</div>
                <?php elseif ($isPopular && !$jaAssinado): ?>
                    <div class="badge-popular">Mais popular</div>
                <?php endif; ?>

                <span class="plan-icon"><?= $info['icon'] ?></span>
                <div class="plan-name"><?= $info['nome'] ?></div>
                <div class="plan-desc"><?= $info['desc'] ?></div>

                <ul class="plan-features">
                    <?php foreach ($info['features'] as $feature): ?>
                    <li><span class="plan-check">✓</span> <?= $feature ?></li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($isCurrent): ?>
                    <div class="btn-current">✓ Plano ativo</div>
                <?php else: ?>
                    <form method="POST" action="subscribe.php">
                        <input type="hidden" name="plano" value="<?= $chave ?>">
                        <?php
                            $btnClass = $isPopular ? 'btn-subscribe-orange' : 'btn-subscribe-primary';
                        ?>
                        <button type="submit" class="btn-subscribe <?= $btnClass ?>">
                            <?= $jaAssinado ? 'Mudar para este plano' : 'Assinar este plano' ?> →
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($jaAssinado): ?>
        <div class="dashboard-link-wrap">
            <a href="<?= APP_URL ?>/index.php?page=dashboard">← Voltar ao Dashboard</a>
        </div>
        <?php endif; ?>

        <p class="security-note">
            🔒 Pagamento processado com segurança pela Stripe &nbsp;·&nbsp;
            Cancele a qualquer momento &nbsp;·&nbsp;
            Dados protegidos com criptografia TLS
        </p>
    </div>

</body>
</html>
