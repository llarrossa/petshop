<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetShop SaaS — Sistema de Gestão para Pet Shops</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #4F46E5;
            --primary-dark: #3730A3;
            --primary-light: #EEF2FF;
            --accent: #10B981;
            --text: #111827;
            --muted: #6B7280;
            --border: #E5E7EB;
            --white: #FFFFFF;
            --bg: #F9FAFB;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text);
            background: var(--white);
            line-height: 1.6;
        }

        a { text-decoration: none; }

        /* NAV */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .nav-logo {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 500;
            transition: color .2s;
        }

        .nav-links a:hover { color: var(--primary); }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,.3); }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        .btn-outline:hover { background: var(--primary-light); }

        .btn-lg { padding: 14px 32px; font-size: 1rem; border-radius: 10px; }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 50%, #059669 100%);
            color: var(--white);
            text-align: center;
            padding: 100px 2rem 120px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            color: var(--white);
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
            max-width: 720px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            opacity: .85;
            max-width: 560px;
            margin: 0 auto 40px;
        }

        .hero-cta {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-white {
            background: var(--white);
            color: var(--primary);
            font-weight: 700;
        }
        .btn-white:hover { background: #F3F4F6; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.15); }

        .btn-ghost {
            background: rgba(255,255,255,.15);
            color: var(--white);
            border: 2px solid rgba(255,255,255,.4);
        }
        .btn-ghost:hover { background: rgba(255,255,255,.25); }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 48px;
            margin-top: 64px;
            flex-wrap: wrap;
        }

        .hero-stat strong {
            display: block;
            font-size: 2rem;
            font-weight: 800;
        }

        .hero-stat span {
            font-size: 0.85rem;
            opacity: .75;
        }

        /* WAVE */
        .wave {
            display: block;
            width: 100%;
            margin-top: -2px;
        }

        /* SECTION */
        section { padding: 80px 2rem; }
        .container { max-width: 1100px; margin: 0 auto; }

        .section-label {
            text-align: center;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .section-title {
            text-align: center;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 800;
            margin-bottom: 16px;
        }

        .section-sub {
            text-align: center;
            color: var(--muted);
            max-width: 560px;
            margin: 0 auto 56px;
            font-size: 1rem;
        }

        /* FEATURES */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 28px;
        }

        .feature-card {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            transition: box-shadow .2s, transform .2s;
        }

        .feature-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,.08);
            transform: translateY(-3px);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* PLANS */
        .plans-section { background: var(--bg); }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
            gap: 28px;
            align-items: start;
        }

        .plan-card {
            background: var(--white);
            border: 2px solid var(--border);
            border-radius: 20px;
            padding: 36px;
            position: relative;
            transition: box-shadow .2s;
        }

        .plan-card:hover { box-shadow: 0 12px 32px rgba(0,0,0,.1); }

        .plan-card.featured {
            border-color: var(--primary);
            box-shadow: 0 8px 32px rgba(79,70,229,.2);
        }

        .plan-badge {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: var(--white);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 16px;
            border-radius: 999px;
            letter-spacing: .05em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .plan-icon { font-size: 2rem; margin-bottom: 16px; display: block; }

        .plan-name {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .plan-desc {
            color: var(--muted);
            font-size: 0.875rem;
            margin-bottom: 24px;
            min-height: 40px;
        }

        .plan-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 32px;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--text);
        }

        .plan-features li::before {
            content: '✓';
            width: 20px;
            height: 20px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* TESTIMONIAL / CTA BANNER */
        .cta-banner {
            background: linear-gradient(135deg, var(--primary) 0%, #7C3AED 100%);
            color: white;
            text-align: center;
            border-radius: 24px;
            padding: 64px 48px;
        }

        .cta-banner h2 {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800;
            margin-bottom: 16px;
        }

        .cta-banner p {
            opacity: .85;
            margin-bottom: 32px;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        /* FOOTER */
        footer {
            background: #111827;
            color: #9CA3AF;
            text-align: center;
            padding: 40px 2rem;
            font-size: 0.875rem;
        }

        footer strong { color: white; }

        @media (max-width: 640px) {
            .nav-links { display: none; }
            .hero { padding: 72px 1.5rem 100px; }
            .hero-stats { gap: 32px; }
            section { padding: 60px 1.25rem; }
            .cta-banner { padding: 48px 24px; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <div class="nav-logo">
        🐾 PetShop SaaS
    </div>
    <ul class="nav-links">
        <li><a href="#funcionalidades">Funcionalidades</a></li>
        <li><a href="#planos">Planos</a></li>
    </ul>
    <a href="public/login.php" class="btn btn-primary">Entrar no Sistema</a>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">🐾 Sistema de Gestão para Pet Shops</div>
    <h1>Gerencie seu Pet Shop<br>de forma simples e completa</h1>
    <p>Agenda, tutores, pets, estoque, vendas e financeiro em um único lugar. Foco no que importa: cuidar dos bichos.</p>
    <div class="hero-cta">
        <a href="public/login.php" class="btn btn-white btn-lg">Acessar o Sistema →</a>
        <a href="#funcionalidades" class="btn btn-ghost btn-lg">Ver funcionalidades</a>
    </div>
    <div class="hero-stats">
        <div class="hero-stat">
            <strong>360°</strong>
            <span>Visão do negócio</span>
        </div>
        <div class="hero-stat">
            <strong>3</strong>
            <span>Planos disponíveis</span>
        </div>
        <div class="hero-stat">
            <strong>100%</strong>
            <span>Web, sem instalar</span>
        </div>
    </div>
</section>

<svg class="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 60" preserveAspectRatio="none">
    <path fill="#F9FAFB" d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z"/>
</svg>

<!-- FUNCIONALIDADES -->
<section id="funcionalidades">
    <div class="container">
        <div class="section-label">Funcionalidades</div>
        <h2 class="section-title">Tudo que seu Pet Shop precisa</h2>
        <p class="section-sub">Do agendamento de banho e tosa ao controle financeiro, cobrindo cada etapa da operação.</p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda de Atendimentos</h3>
                <p>Gerencie agendamentos com horário, profissional, serviço e status em tempo real. Evite conflitos e faltas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Cadastro de Tutores</h3>
                <p>Ficha completa com dados de contato, histórico de compras e todos os pets vinculados a cada cliente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🐕</div>
                <h3>Prontuário de Pets</h3>
                <p>Espécie, raça, peso, idade e observações. Prontuário completo para cada animal com histórico de atendimentos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Controle de Estoque</h3>
                <p>Entradas, saídas, ajustes e alertas de estoque mínimo. Nunca fique sem produto na hora errada.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>PDV e Vendas</h3>
                <p>Ponto de venda integrado com múltiplas formas de pagamento, desconto e emissão de comprovante.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Relatórios e Financeiro</h3>
                <p>Faturamento por período, produtos mais vendidos, serviços e muito mais para tomar as melhores decisões.</p>
            </div>
        </div>
    </div>
</section>

<!-- PLANOS -->
<section id="planos" class="plans-section">
    <div class="container">
        <div class="section-label">Planos</div>
        <h2 class="section-title">Escolha o plano ideal</h2>
        <p class="section-sub">Cada negócio tem seu perfil. Escolha o módulo certo e pague apenas pelo que usar.</p>

        <div class="plans-grid">

            <div class="plan-card">
                <span class="plan-icon">✂️</span>
                <div class="plan-name">Banho & Tosa</div>
                <div class="plan-desc">Ideal para pet shops focados em serviços de estética e banho.</div>
                <ul class="plan-features">
                    <li>Agenda de atendimentos</li>
                    <li>Cadastro de tutores</li>
                    <li>Prontuário de pets</li>
                    <li>Cadastro de serviços</li>
                    <li>Gestão de profissionais</li>
                    <li>Dashboard gerencial</li>
                </ul>
                <a href="public/login.php" class="btn btn-outline" style="width:100%; justify-content:center;">Começar agora</a>
            </div>

            <div class="plan-card featured">
                <div class="plan-badge">Mais Popular</div>
                <span class="plan-icon">🏪</span>
                <div class="plan-name">Loja / AgroPet</div>
                <div class="plan-desc">Para lojas e agroPets focados em venda de produtos e controle de estoque.</div>
                <ul class="plan-features">
                    <li>Controle de produtos e estoque</li>
                    <li>PDV — Ponto de Venda</li>
                    <li>Gestão de estoque</li>
                    <li>Módulo financeiro</li>
                    <li>Relatórios de vendas</li>
                    <li>Dashboard gerencial</li>
                </ul>
                <a href="public/login.php" class="btn btn-primary" style="width:100%; justify-content:center;">Começar agora</a>
            </div>

            <div class="plan-card">
                <span class="plan-icon">🐾</span>
                <div class="plan-name">Completo</div>
                <div class="plan-desc">Tudo em um único sistema para o Pet Shop que quer crescer sem limites.</div>
                <ul class="plan-features">
                    <li>Todos os módulos incluídos</li>
                    <li>Agenda + PDV integrados</li>
                    <li>Tutores, pets e prontuário</li>
                    <li>Estoque e financeiro</li>
                    <li>Relatórios completos</li>
                    <li>Suporte prioritário</li>
                </ul>
                <a href="public/login.php" class="btn btn-outline" style="width:100%; justify-content:center;">Começar agora</a>
            </div>

        </div>
    </div>
</section>

<!-- CTA FINAL -->
<section>
    <div class="container">
        <div class="cta-banner">
            <h2>Pronto para organizar seu Pet Shop?</h2>
            <p>Acesse agora e descubra como é simples gerenciar tudo em um único lugar.</p>
            <a href="public/login.php" class="btn btn-white btn-lg">Entrar no Sistema →</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <p><strong>🐾 PetShop SaaS</strong> &mdash; Sistema de Gestão para Pet Shops</p>
    <p style="margin-top:8px;">© <?= date('Y') ?> Todos os direitos reservados.</p>
</footer>

</body>
</html>
