<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Pet Shop SaaS' ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- Overlay mobile -->
        <div class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <button class="sidebar-close" aria-label="Fechar menu">✕</button>
            <div class="logo">
                <a href="?page=dashboard">
                    <img src="<?= APP_URL ?>/public/img/dark-horizontal.png" alt="Pawfy" style="max-height: 78px;max-width:100%;display:block;place-self: center;">
                </a>
                <p><?= $_SESSION['company_name'] ?? 'Sistema' ?></p>
            </div>

            <nav class="menu">
                <ul>
                    <li><a href="?page=dashboard"><span>📊</span> Dashboard</a></li>

                    <?php if (moduloDisponivel('clientes')): ?>
                    <li><a href="?page=clientes"><span>👥</span> Clientes</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('pets')): ?>
                    <li><a href="?page=pets"><span>🐕</span> Pets</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('servicos')): ?>
                    <li><a href="?page=servicos"><span>✂️</span> Serviços</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('produtos')): ?>
                    <li><a href="?page=produtos"><span>📦</span> Produtos</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('agenda')): ?>
                    <li><a href="?page=agenda"><span>📅</span> Agenda</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('vendas')): ?>
                    <li><a href="?page=vendas"><span>💰</span> Vendas (PDV)</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('financeiro')): ?>
                    <li><a href="?page=financeiro"><span>💵</span> Financeiro</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('profissionais')): ?>
                    <li><a href="?page=profissionais"><span>👨‍⚕️</span> Profissionais</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('prontuario')): ?>
                    <li><a href="?page=prontuario"><span>📋</span> Prontuário</a></li>
                    <?php endif; ?>

                    <?php if (moduloDisponivel('relatorios')): ?>
                    <li><a href="?page=relatorios"><span>📈</span> Relatórios</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="user-info">
                <p><strong><?= $_SESSION['user_name'] ?? 'Usuário' ?></strong></p>
                <p><?= $_SESSION['user_email'] ?? '' ?></p>
                <?php if (($_SESSION['subscription_status'] ?? '') === 'trialing'): ?>
                <a href="<?= APP_URL ?>/public/planos.php" class="btn-assinar">Assinar um plano</a>
                <?php endif; ?>
                <a href="?page=conta" class="btn-conta">Minha Conta</a>
                <a href="<?= APP_URL ?>/public/logout.php" class="btn-logout">Sair</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <header class="top-bar">
                <div style="display:flex; align-items:center; gap:12px;">
                    <button class="btn-toggle-sidebar" aria-label="Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1><?= $page_title ?? 'Pet Shop SaaS' ?></h1>
                </div>
                <div class="user-actions">
                    <div class="plano-switcher">
                        <span class="plano-label">Plano:</span>
                        <div class="plano-dropdown">
                            <button class="plano-btn">
                                <?= PLANOS[$_SESSION['plano'] ?? 'completo']['nome'] ?> ▾
                            </button>
                            <div class="plano-menu">
                                <div class="plano-menu-header">Trocar plano (teste)</div>
                                <?php foreach (PLANOS as $key => $plano): ?>
                                <a href="?trocar_plano=<?= $key ?>"
                                   class="plano-menu-item <?= ($_SESSION['plano'] ?? 'completo') === $key ? 'active' : '' ?>">
                                    <?= $plano['nome'] ?>
                                    <?php if (($_SESSION['plano'] ?? 'completo') === $key): ?>
                                    <span class="plano-check">✓</span>
                                    <?php endif; ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <?php if (($_SESSION['subscription_status'] ?? '') === 'trialing'): ?>
            <?php $diasTrial = diasRestantesTrial(); ?>
            <div class="trial-banner">
                <div class="trial-banner-content">
                    <span class="trial-banner-icon">⏳</span>
                    <span class="trial-banner-text">
                        Você está no <strong>período de teste gratuito</strong>.
                        <?php if ($diasTrial > 1): ?>
                            Faltam <strong><?= $diasTrial ?> dias</strong> para expirar.
                        <?php elseif ($diasTrial === 1): ?>
                            <strong>Último dia</strong> do seu teste!
                        <?php else: ?>
                            Seu teste <strong>expira hoje</strong>.
                        <?php endif; ?>
                    </span>
                    <a href="<?= APP_URL ?>/public/planos.php" class="trial-banner-btn">Assinar agora</a>
                </div>
            </div>
            <style>
                .trial-banner {
                    background: linear-gradient(90deg, #1E3A8A, #2563EB);
                    color: #fff;
                    padding: 10px 24px;
                }
                .trial-banner-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex-wrap: wrap;
                    font-size: 0.88rem;
                }
                .trial-banner-icon { font-size: 1rem; }
                .trial-banner-text { flex: 1; min-width: 200px; }
                .trial-banner-btn {
                    background: #fff;
                    color: #2563EB;
                    padding: 5px 16px;
                    border-radius: 6px;
                    font-weight: 700;
                    font-size: 0.82rem;
                    text-decoration: none;
                    white-space: nowrap;
                    transition: background .15s;
                }
                .trial-banner-btn:hover { background: #EFF6FF; }
            </style>
            <?php endif; ?>

            <div class="main-content">
                <!-- Mensagens de feedback -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success'] ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
                <?php endif; ?>

                <!-- Conteúdo da página -->
                <?php if (isset($content)) echo $content; ?>
            </div>
        </main>
    </div>

    <script src="<?= APP_URL ?>/public/js/script.js"></script>
</body>
</html>
