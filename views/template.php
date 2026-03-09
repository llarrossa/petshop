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
                <h2>🐾 Pet Shop</h2>
                <p><?= $_SESSION['company_name'] ?? 'Sistema' ?></p>
            </div>

            <nav class="menu">
                <ul>
                    <li><a href="?page=dashboard"><span>📊</span> Dashboard</a></li>

                    <?php if (moduloDisponivel('tutores')): ?>
                    <li><a href="?page=tutores"><span>👥</span> Tutores</a></li>
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

                    <?php if (moduloDisponivel('relatorios')): ?>
                    <li><a href="?page=relatorios"><span>📈</span> Relatórios</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="user-info">
                <p><strong><?= $_SESSION['user_name'] ?? 'Usuário' ?></strong></p>
                <p><?= $_SESSION['user_email'] ?? '' ?></p>
                <a href="public/logout.php" class="btn-logout">Sair</a>
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
                    <span>Plano: <strong><?= PLANOS[$_SESSION['plano'] ?? 'completo']['nome'] ?></strong></span>
                </div>
            </header>

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
