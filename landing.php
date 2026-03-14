<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pawfy — Gestão completa para Pet Shops</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue: #2563EB;
            --blue-dark: #1D4ED8;
            --blue-light: #EFF6FF;
            --orange: #F97316;
            --orange-dark: #EA6C0A;
            --orange-light: #FFF7ED;
            --bg: #F8FAFC;
            --text: #1E293B;
            --muted: #64748B;
            --border: #E2E8F0;
            --white: #FFFFFF;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text);
            background: var(--white);
            line-height: 1.6;
        }

        a { text-decoration: none; color: inherit; }

        .container { max-width: 1140px; margin: 0 auto; padding: 0 24px; }

        /* ── BOTÕES ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all .18s;
            white-space: nowrap;
        }
        .btn-orange { background: var(--orange); color: var(--white); }
        .btn-orange:hover { background: var(--orange-dark); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,.35); }
        .btn-outline-white { background: transparent; color: var(--white); border: 2px solid rgba(255,255,255,.55); }
        .btn-outline-white:hover { background: rgba(255,255,255,.12); }
        .btn-blue { background: var(--blue); color: var(--white); }
        .btn-blue:hover { background: var(--blue-dark); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,99,235,.35); }
        .btn-outline-blue { background: transparent; color: var(--blue); border: 2px solid var(--blue); }
        .btn-outline-blue:hover { background: var(--blue-light); }
        .btn-lg { padding: 16px 36px; font-size: 1.05rem; border-radius: 10px; }

        /* ── NAV ── */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgb(231 240 247);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
        }
        .nav-inner {
            display: flex; align-items: center;
            justify-content: space-between;
            height: 68px; gap: 24px;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            font-size: 1.35rem; font-weight: 800;
            color: var(--blue); letter-spacing: -.02em;
        }
        .nav-logo span { color: var(--orange); }
        .nav-links { display: flex; gap: 32px; list-style: none; }
        .nav-links a { color: var(--muted); font-size: 0.9rem; font-weight: 500; transition: color .15s; }
        .nav-links a:hover { color: var(--blue); }
        .nav-cta { display: flex; align-items: center; gap: 12px; }
        .nav-login { color: var(--blue); font-size: 0.9rem; font-weight: 600; }
        .nav-login:hover { text-decoration: underline; }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 55%, #1D4ED8 100%);
            color: var(--white);
            padding: 90px 0 100px;
            overflow: hidden;
            position: relative;
        }
        .hero::after {
            content: '';
            position: absolute; bottom: -1px; left: 0; right: 0;
            height: 80px; background: #e7f0f7;
            clip-path: ellipse(55% 100% at 50% 100%);
        }
        .hero-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px; align-items: center;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            padding: 6px 14px; border-radius: 999px;
            font-size: 0.78rem; font-weight: 600;
            letter-spacing: .05em; text-transform: uppercase;
            margin-bottom: 24px;
        }
        .hero h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800; line-height: 1.15;
            margin-bottom: 20px; letter-spacing: -.02em;
        }
        .hero h1 em { font-style: normal; color: #FCD34D; }
        .hero p { font-size: 1.1rem; opacity: .85; margin-bottom: 36px; max-width: 480px; line-height: 1.7; }
        .hero-btns { display: flex; gap: 14px; flex-wrap: wrap; }
        .hero-trust { margin-top: 32px; font-size: 0.82rem; opacity: .7; }

        /* MOCKUP */
        .mockup-window {
            background: #1E293B; border-radius: 14px; overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,.4);
        }
        .mockup-bar {
            background: #0F172A; padding: 10px 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-body { display: flex; height: 300px; }
        .mockup-sidebar {
            width: 52px; background: #0F172A;
            padding: 12px 8px; display: flex; flex-direction: column; gap: 6px;
        }
        .mockup-nav-item { height: 34px; border-radius: 6px; background: rgba(255,255,255,.06); }
        .mockup-nav-item.active { background: #2563EB; }
        .mockup-content {
            flex: 1; background: #F8FAFC;
            padding: 14px; display: flex; flex-direction: column; gap: 10px;
        }
        .mockup-header { height: 28px; background: #E2E8F0; border-radius: 6px; width: 55%; }
        .mockup-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; }
        .mockup-card {
            height: 54px; border-radius: 8px; background: white;
            border: 1px solid #E2E8F0; padding: 8px;
            display: flex; flex-direction: column; gap: 4px;
        }
        .mockup-card-line { height: 8px; border-radius: 4px; background: #E2E8F0; }
        .mockup-card-line.accent { background: #2563EB; width: 60%; }
        .mockup-card-line.orange { background: #F97316; width: 40%; }
        .mockup-table {
            background: white; border-radius: 8px;
            border: 1px solid #E2E8F0; flex: 1;
            padding: 10px; display: flex; flex-direction: column; gap: 6px;
        }
        .mockup-row { height: 20px; border-radius: 4px; background: #F8FAFC; border: 1px solid #E2E8F0; }
        .mockup-row:first-child { background: #EFF6FF; border-color: #BFDBFE; }

        /* ── SEÇÃO ── */
        section { padding: 88px 0; background: #e7f0f7 }
        .section-eyebrow {
            display: inline-block; font-size: 0.75rem; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--orange); margin-bottom: 12px;
        }
        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 800;
            line-height: 1.2; letter-spacing: -.02em; margin-bottom: 16px;
        }
        .section-sub { color: var(--muted); font-size: 1.05rem; max-width: 560px; line-height: 1.7; }
        .text-center { text-align: center; }
        .text-center .section-sub { margin: 0 auto; }

        /* ── PROBLEMAS ── */
        .problems { background: #e7f0f7 }
        .problems-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
        .problems-list { display: flex; flex-direction: column; gap: 14px; margin-top: 36px; }
        .problem-item {
            display: flex; align-items: flex-start; gap: 14px;
            background: #f7f7f7; border: 1px solid var(--border);
            border-left: 4px solid #FCA5A5; border-radius: 10px;
            padding: 14px 18px; transition: box-shadow .2s;
        }
        .problem-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }
        .problem-icon { font-size: 1.3rem; flex-shrink: 0; margin-top: 2px; }
        .problem-item p { font-size: 0.9rem; line-height: 1.5; color: var(--text); }
        .problem-item strong { display: block; font-weight: 700; margin-bottom: 2px; }
        .problems-solution {
            background: linear-gradient(135deg, var(--blue) 0%, #1D4ED8 100%);
            color: white; border-radius: 20px; padding: 48px 40px;
        }
        .problems-solution h3 { font-size: 1.5rem; font-weight: 800; margin-bottom: 16px; line-height: 1.3; }
        .problems-solution p { opacity: .85; line-height: 1.7; margin-bottom: 28px; font-size: 0.95rem; }

        /* ── BENEFÍCIOS ── */
        .benefits-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr));
            gap: 24px; margin-top: 56px;
        }
        .benefit-card {
            background: #f7f7f7; border: 1px solid var(--border);
            border-radius: 16px; padding: 32px 28px;
            transition: box-shadow .2s, transform .2s;
        }
        .benefit-card:hover { box-shadow: 0 12px 32px rgba(37,99,235,.1); transform: translateY(-4px); }
        .benefit-icon {
            width: 54px; height: 54px; background: var(--blue-light);
            border-radius: 14px; display: flex; align-items: center;
            justify-content: center; font-size: 1.5rem; margin-bottom: 20px;
        }
        .benefit-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 10px; }
        .benefit-card p { color: var(--muted); font-size: 0.875rem; line-height: 1.6; }

        /* ── DEMO ── */
        .demo { background: #e7f0f7; }
        .demo-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; margin-top: 56px; }
        .demo-card {
            background: white; border: 1px solid var(--border);
            border-radius: 16px; overflow: hidden; transition: box-shadow .2s;
        }
        .demo-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.09); }
        .demo-card-header {
            background: var(--blue); padding: 10px 18px;
            display: flex; align-items: center; gap: 8px;
        }
        .demo-card-header span { font-size: 0.8rem; font-weight: 600; color: white; }
        .demo-card-body { padding: 18px; display: flex; flex-direction: column; gap: 8px; min-height: 130px; }
        .demo-row { display: flex; gap: 10px; }
        .demo-cell {
            height: 22px; border-radius: 5px;
            background: var(--bg); border: 1px solid var(--border); flex: 1;
        }
        .demo-cell.blue { background: #DBEAFE; border-color: #BFDBFE; }
        .demo-cell.orange { background: #FFEDD5; border-color: #FED7AA; }
        .demo-cell.green { background: #D1FAE5; border-color: #A7F3D0; }
        .demo-cell.sm { flex: 0 0 60px; }
        .demo-cell.xs { flex: 0 0 40px; }
        .demo-tagline { text-align: center; color: var(--muted); font-size: 0.9rem; margin-top: 24px; }

        /* ── PLANOS ── */
        .plans-grid {
            display: grid; grid-template-columns: repeat(3,1fr);
            gap: 24px; margin-top: 56px; align-items: start;
        }
        .plan-card {
            background: #f7f7f7; border: 2px solid var(--border);
            border-radius: 20px; padding: 36px 32px;
            position: relative; transition: box-shadow .2s;
        }
        .plan-card:hover { box-shadow: 0 12px 40px rgba(0,0,0,.09); }
        .plan-card.featured { border-color: var(--orange); box-shadow: 0 8px 40px rgba(249,115,22,.18); }
        .plan-popular {
            position: absolute; top: -15px; left: 50%; transform: translateX(-50%);
            background: var(--orange); color: white;
            font-size: 0.72rem; font-weight: 800;
            padding: 4px 18px; border-radius: 999px;
            letter-spacing: .06em; text-transform: uppercase; white-space: nowrap;
        }
        .plan-icon { font-size: 2.2rem; margin-bottom: 16px; display: block; }
        .plan-name { font-size: 1.2rem; font-weight: 800; margin-bottom: 6px; }
        .plan-price { font-size: 2rem; font-weight: 800; color: #1E293B; margin-bottom: 4px; line-height: 1; }
        .plan-price-period { font-size: 0.88rem; font-weight: 400; color: var(--muted); }
        .plan-desc { color: var(--muted); font-size: 0.85rem; line-height: 1.55; margin-top: 8px; margin-bottom: 28px; min-height: 44px; }
        .plan-features { list-style: none; display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px; }
        .plan-features li { display: flex; align-items: center; gap: 10px; font-size: 0.875rem; }
        .plan-check {
            width: 20px; height: 20px; border-radius: 50%;
            background: #D1FAE5; color: #059669;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 800; flex-shrink: 0;
        }
        .plan-card.featured .plan-check { background: #FFEDD5; color: var(--orange); }
        .plan-cta { width: 100%; justify-content: center; border-radius: 10px; padding: 13px; }

        /* ── DEPOIMENTOS ── */
        .testimonials { background: #e7f0f7; }
        .testimonials-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; margin-top: 56px; }
        .testimonial-card {
            background: #f7f7f7; border: 1px solid var(--border);
            border-radius: 16px; padding: 32px; transition: box-shadow .2s;
        }
        .testimonial-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.07); }
        .testimonial-stars { color: #FBBF24; font-size: 1.1rem; margin-bottom: 18px; letter-spacing: 2px; }
        .testimonial-text { font-size: 0.9rem; line-height: 1.7; margin-bottom: 24px; font-style: italic; }
        .testimonial-author { display: flex; align-items: center; gap: 12px; }
        .testimonial-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .testimonial-name { font-weight: 700; font-size: 0.9rem; }
        .testimonial-role { font-size: 0.78rem; color: var(--muted); }

        /* ── CTA FINAL ── */
        .cta-final { background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 60%, #1D4ED8 100%); color: white; }
        .cta-final-inner { text-align: center; padding: 88px 0; }
        .cta-final h2 { font-size: clamp(1.8rem, 3.5vw, 2.8rem); font-weight: 800; margin-bottom: 18px; line-height: 1.2; }
        .cta-final p { font-size: 1.1rem; opacity: .8; max-width: 480px; margin: 0 auto 40px; line-height: 1.7; }
        .cta-trust { margin-top: 24px; font-size: 0.82rem; opacity: .6; }

        /* ── FOOTER ── */
        footer { background: #09152b; color: #94A3B8; padding: 64px 0 32px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 56px; }
        .footer-brand p { font-size: 0.875rem; line-height: 1.7; margin: 12px 0 24px; max-width: 260px; }
        .footer-social { display: flex; gap: 10px; }
        .footer-social a {
            width: 38px; height: 38px; background: rgba(255,255,255,.08);
            border-radius: 8px; display: flex; align-items: center;
            justify-content: center; font-size: 1rem; transition: background .15s;
        }
        .footer-social a:hover { background: var(--blue); }
        .footer-col h4 { font-size: 0.8rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: white; margin-bottom: 16px; }
        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-col a { font-size: 0.875rem; color: #94A3B8; transition: color .15s; }
        .footer-col a:hover { color: white; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.08); padding-top: 28px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
        }
        .footer-bottom p { font-size: 0.825rem; }
        .footer-bottom-links { display: flex; gap: 24px; }
        .footer-bottom-links a { font-size: 0.825rem; color: #94A3B8; transition: color .15s; }
        .footer-bottom-links a:hover { color: white; }

        /* ── RESPONSIVO ── */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; }
            .hero-mockup { display: none; }
            .plans-grid { grid-template-columns: 1fr; max-width: 440px; margin-left: auto; margin-right: auto; }
            .testimonials-grid { grid-template-columns: 1fr; max-width: 520px; margin-left: auto; margin-right: auto; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .problems-inner { grid-template-columns: 1fr; }
            .demo-grid { grid-template-columns: 1fr; }
            section { padding: 64px 0; }
        }
        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr; }
            .hero-btns { flex-direction: column; }
        }
    </style>
</head>
<body>

<!-- ══ NAV ══ -->
<nav>
    <div class="container nav-inner">
        <a href="#" class="nav-logo">
            <img src="public/img/primary-logo.png" alt="Pawfy" style="height:69px;display:block;">
        </a>
        <ul class="nav-links">
            <li><a href="#problemas">Por que o Pawfy?</a></li>
            <li><a href="#beneficios">Funcionalidades</a></li>
            <li><a href="#planos">Planos</a></li>
            <li><a href="#depoimentos">Depoimentos</a></li>
        </ul>
        <div class="nav-cta">
            <a href="public/login.php" class="nav-login">Entrar</a>
            <a href="public/register.php" class="btn btn-orange">Começar grátis</a>
        </div>
    </div>
</nav>

<!-- ══ HERO ══ -->
<section class="hero">
    <div class="container hero-inner">
        <div>
            <div class="hero-badge">🐾 Feito para Pet Shops brasileiros</div>
            <h1>Gestão completa para <em>Pet Shops</em></h1>
            <p>Agenda, vendas, estoque e controle financeiro em um único sistema simples de usar. Sem complicação, sem planilha, sem papel.</p>
            <div class="hero-btns">
                <a href="public/register.php" class="btn btn-orange btn-lg">Começar teste grátis</a>
                <a href="#beneficios" class="btn btn-outline-white btn-lg">Ver demonstração</a>
            </div>
            <div class="hero-trust">✓ Sem cartão de crédito &nbsp;·&nbsp; ✓ Configuração em minutos &nbsp;·&nbsp; ✓ Cancele quando quiser</div>
        </div>

        <div class="hero-mockup">
            <div class="mockup-window">
                <div class="mockup-bar">
                    <div class="mockup-dot" style="background:#EF4444"></div>
                    <div class="mockup-dot" style="background:#F59E0B"></div>
                    <div class="mockup-dot" style="background:#10B981"></div>
                </div>
                <div class="mockup-body">
                    <div class="mockup-sidebar">
                        <div class="mockup-nav-item active"></div>
                        <div class="mockup-nav-item"></div>
                        <div class="mockup-nav-item"></div>
                        <div class="mockup-nav-item"></div>
                        <div class="mockup-nav-item"></div>
                        <div class="mockup-nav-item"></div>
                    </div>
                    <div class="mockup-content">
                        <div class="mockup-header"></div>
                        <div class="mockup-cards">
                            <div class="mockup-card"><div class="mockup-card-line accent"></div><div class="mockup-card-line" style="width:80%"></div></div>
                            <div class="mockup-card"><div class="mockup-card-line orange"></div><div class="mockup-card-line" style="width:70%"></div></div>
                            <div class="mockup-card"><div class="mockup-card-line accent"></div><div class="mockup-card-line" style="width:60%"></div></div>
                            <div class="mockup-card"><div class="mockup-card-line" style="background:#10B981;width:50%"></div><div class="mockup-card-line" style="width:75%"></div></div>
                        </div>
                        <div class="mockup-table">
                            <div class="mockup-row"></div>
                            <div class="mockup-row"></div>
                            <div class="mockup-row"></div>
                            <div class="mockup-row"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══ PROBLEMAS ══ -->
<section class="problems" id="problemas">
    <div class="container problems-inner">
        <div>
            <span class="section-eyebrow">A realidade de muitos pet shops</span>
            <h2 class="section-title">Você se identifica com algum desses problemas?</h2>
            <div class="problems-list">
                <div class="problem-item">
                    <span class="problem-icon">📅</span>
                    <p><strong>Agenda desorganizada</strong>Clientes chegando no horário errado, sobreposição de atendimentos e confusão no dia a dia.</p>
                </div>
                <div class="problem-item">
                    <span class="problem-icon">😓</span>
                    <p><strong>Clientes esquecendo o banho do pet</strong>Sem lembretes, você perde recorrência e receita sem perceber.</p>
                </div>
                <div class="problem-item">
                    <span class="problem-icon">📦</span>
                    <p><strong>Estoque sem controle</strong>Produtos em falta na hora da venda e dinheiro parado em mercadoria desnecessária.</p>
                </div>
                <div class="problem-item">
                    <span class="problem-icon">📝</span>
                    <p><strong>Vendas anotadas no papel ou planilha</strong>Dados perdidos, erros e impossibilidade de analisar o desempenho do negócio.</p>
                </div>
                <div class="problem-item">
                    <span class="problem-icon">💸</span>
                    <p><strong>Dificuldade para acompanhar o faturamento</strong>Sem visibilidade financeira, é impossível saber se o negócio está crescendo.</p>
                </div>
            </div>
        </div>

        <div class="problems-solution">
            <h3>O Pawfy centraliza tudo em um único lugar.</h3>
            <p>Chega de controles separados, dados perdidos e horas desperdiçadas. Com o Pawfy, você gerencia agenda, clientes, estoque, vendas e financeiro em uma plataforma simples e feita para a rotina do pet shop.</p>
            <a href="public/register.php" class="btn btn-orange">Quero experimentar grátis →</a>
        </div>
    </div>
</section>

<!-- ══ BENEFÍCIOS ══ -->
<section id="beneficios">
    <div class="container">
        <div class="text-center">
            <span class="section-eyebrow">Funcionalidades</span>
            <h2 class="section-title">Tudo que seu pet shop precisa</h2>
            <p class="section-sub">Módulos integrados que trabalham juntos para deixar sua operação mais organizada e seu negócio mais lucrativo.</p>
        </div>
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon">📅</div>
                <h3>Agenda inteligente</h3>
                <p>Organize todos os atendimentos de banho e tosa com facilidade. Visualize o dia e evite conflitos de horário.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">📦</div>
                <h3>Controle de estoque</h3>
                <p>Saiba exatamente quais produtos estão em falta. Alertas de estoque mínimo para nunca perder uma venda.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">💰</div>
                <h3>Vendas rápidas</h3>
                <p>Registre vendas de produtos e serviços pelo PDV. Múltiplas formas de pagamento e emissão de comprovante.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">📊</div>
                <h3>Relatórios claros</h3>
                <p>Acompanhe o faturamento e desempenho do pet shop com gráficos fáceis de entender.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">🐕</div>
                <h3>Ficha completa dos pets</h3>
                <p>Prontuário com histórico de atendimentos, observações e dados do tutor sempre à mão.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">👥</div>
                <h3>Gestão de clientes</h3>
                <p>Cadastro completo de tutores com histórico de compras e agendamentos para um atendimento personalizado.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══ DEMO ══ -->
<section class="demo" id="demo">
    <div class="container">
        <div class="text-center">
            <span class="section-eyebrow">Sistema</span>
            <h2 class="section-title">Criado para a rotina do pet shop</h2>
            <p class="section-sub">Interface limpa e intuitiva. Você e sua equipe aprendem em minutos, sem precisar de treinamento.</p>
        </div>
        <div class="demo-grid">
            <div class="demo-card">
                <div class="demo-card-header"><span>📅 Agenda do dia</span></div>
                <div class="demo-card-body">
                    <div class="demo-row"><div class="demo-cell blue"></div><div class="demo-cell sm"></div><div class="demo-cell xs"></div></div>
                    <div class="demo-row"><div class="demo-cell green"></div><div class="demo-cell sm"></div><div class="demo-cell xs"></div></div>
                    <div class="demo-row"><div class="demo-cell orange"></div><div class="demo-cell sm"></div><div class="demo-cell xs"></div></div>
                    <div class="demo-row"><div class="demo-cell blue"></div><div class="demo-cell sm"></div><div class="demo-cell xs"></div></div>
                </div>
            </div>
            <div class="demo-card">
                <div class="demo-card-header"><span>💰 PDV — Vendas</span></div>
                <div class="demo-card-body">
                    <div class="demo-row"><div class="demo-cell"></div><div class="demo-cell xs"></div></div>
                    <div class="demo-row"><div class="demo-cell"></div><div class="demo-cell xs"></div></div>
                    <div class="demo-row"><div class="demo-cell orange" style="flex:0 0 100px"></div><div class="demo-cell"></div></div>
                    <div class="demo-row"><div class="demo-cell blue"></div></div>
                </div>
            </div>
            <div class="demo-card">
                <div class="demo-card-header"><span>📦 Estoque</span></div>
                <div class="demo-card-body">
                    <div class="demo-row"><div class="demo-cell blue" style="flex:0 0 30px"></div><div class="demo-cell"></div><div class="demo-cell xs"></div></div>
                    <div class="demo-row"><div class="demo-cell blue" style="flex:0 0 30px"></div><div class="demo-cell"></div><div class="demo-cell xs"></div></div>
                    <div class="demo-row"><div class="demo-cell orange" style="flex:0 0 30px"></div><div class="demo-cell"></div><div class="demo-cell xs"></div></div>
                    <div class="demo-row"><div class="demo-cell blue" style="flex:0 0 30px"></div><div class="demo-cell"></div><div class="demo-cell xs"></div></div>
                </div>
            </div>
            <div class="demo-card">
                <div class="demo-card-header"><span>📊 Dashboard</span></div>
                <div class="demo-card-body">
                    <div class="demo-row"><div class="demo-cell blue"></div><div class="demo-cell orange"></div></div>
                    <div class="demo-row"><div class="demo-cell green"></div><div class="demo-cell"></div></div>
                    <div class="demo-row" style="gap:6px;align-items:flex-end">
                        <div class="demo-cell" style="height:40px;background:linear-gradient(to top,#2563EB 60%,#DBEAFE);border:none;border-radius:4px 4px 0 0"></div>
                        <div class="demo-cell" style="height:28px;background:linear-gradient(to top,#F97316 45%,#FFEDD5);border:none;border-radius:4px 4px 0 0"></div>
                        <div class="demo-cell" style="height:50px;background:linear-gradient(to top,#2563EB 75%,#DBEAFE);border:none;border-radius:4px 4px 0 0"></div>
                        <div class="demo-cell" style="height:34px;background:linear-gradient(to top,#F97316 55%,#FFEDD5);border:none;border-radius:4px 4px 0 0"></div>
                        <div class="demo-cell" style="height:44px;background:linear-gradient(to top,#2563EB 65%,#DBEAFE);border:none;border-radius:4px 4px 0 0"></div>
                    </div>
                </div>
            </div>
        </div>
        <p class="demo-tagline">🔒 Seus dados seguros e acessíveis de qualquer dispositivo, a qualquer hora.</p>
    </div>
</section>

<!-- ══ PLANOS ══ -->
<section id="planos">
    <div class="container">
        <div class="text-center">
            <span class="section-eyebrow">Planos</span>
            <h2 class="section-title">Escolha o plano ideal para seu negócio</h2>
            <p class="section-sub">Cada pet shop tem um perfil único. Comece com o que precisa agora e evolua quando quiser.</p>
        </div>
        <div class="plans-grid">
            <div class="plan-card">
                <span class="plan-icon">✂️</span>
                <div class="plan-name">Banho & Tosa</div>
                <div class="plan-price">R$ 49<span class="plan-price-period">/mês</span></div>
                <div class="plan-desc">Para pet shops focados em serviços de estética e atendimento animal.</div>
                <ul class="plan-features">
                    <li><span class="plan-check">✓</span> Agenda de atendimentos</li>
                    <li><span class="plan-check">✓</span> Cadastro de clientes</li>
                    <li><span class="plan-check">✓</span> Ficha completa dos pets</li>
                    <li><span class="plan-check">✓</span> Cadastro de serviços</li>
                    <li><span class="plan-check">✓</span> Gestão de profissionais</li>
                    <li><span class="plan-check">✓</span> Dashboard gerencial</li>
                </ul>
                <a href="public/register.php" class="btn btn-outline-blue plan-cta">Começar agora</a>
            </div>

            <div class="plan-card featured">
                <div class="plan-popular">Mais popular</div>
                <span class="plan-icon">🏪</span>
                <div class="plan-name">Pet Shop</div>
                <div class="plan-price">R$ 79<span class="plan-price-period">/mês</span></div>
                <div class="plan-desc">Para lojas focadas em vendas de produtos e controle de estoque.</div>
                <ul class="plan-features">
                    <li><span class="plan-check">✓</span> Cadastro de produtos</li>
                    <li><span class="plan-check">✓</span> PDV — Ponto de Venda</li>
                    <li><span class="plan-check">✓</span> Controle de estoque</li>
                    <li><span class="plan-check">✓</span> Controle financeiro</li>
                    <li><span class="plan-check">✓</span> Relatórios de vendas</li>
                    <li><span class="plan-check">✓</span> Dashboard gerencial</li>
                </ul>
                <a href="public/register.php" class="btn btn-orange plan-cta">Começar agora</a>
            </div>

            <div class="plan-card">
                <span class="plan-icon">🐾</span>
                <div class="plan-name">Completo</div>
                <div class="plan-price">R$ 119<span class="plan-price-period">/mês</span></div>
                <div class="plan-desc">Para pet shops completos com serviços e venda de produtos integrados.</div>
                <ul class="plan-features">
                    <li><span class="plan-check">✓</span> Todos os módulos incluídos</li>
                    <li><span class="plan-check">✓</span> Agenda + PDV integrados</li>
                    <li><span class="plan-check">✓</span> Clientes, pets e prontuário</li>
                    <li><span class="plan-check">✓</span> Estoque e financeiro</li>
                    <li><span class="plan-check">✓</span> Relatórios completos</li>
                    <li><span class="plan-check">✓</span> Suporte prioritário</li>
                </ul>
                <a href="public/register.php" class="btn btn-outline-blue plan-cta">Começar agora</a>
            </div>
        </div>
    </div>
</section>

<!-- ══ DEPOIMENTOS ══ -->
<section class="testimonials" id="depoimentos">
    <div class="container">
        <div class="text-center">
            <span class="section-eyebrow">Depoimentos</span>
            <h2 class="section-title">Pet shops que já organizam o negócio com o Pawfy</h2>
            <p class="section-sub">Veja o que donos de pet shop dizem sobre como o Pawfy transformou a rotina deles.</p>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Antes eu controlava tudo em caderninho e planilha. Depois do Pawfy, a agenda ficou organizada, não perco mais horário e consigo ver quanto faturei no mês em segundos."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar" style="background:#DBEAFE">🧑</div>
                    <div>
                        <div class="testimonial-name">Carlos Mendes</div>
                        <div class="testimonial-role">Dono — Pet Shop Amigos do Rex, SP</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"O controle de estoque mudou tudo pra mim. Recebia alerta de produto acabando antes que percebesse, e parei de perder venda por falta de mercadoria."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar" style="background:#D1FAE5">👩</div>
                    <div>
                        <div class="testimonial-name">Ana Paula Souza</div>
                        <div class="testimonial-role">Proprietária — AgroPet Natureza, MG</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Sistema simples de verdade. Minha equipe aprendeu a usar no mesmo dia. A agenda e o PDV integrados economizam muito tempo no atendimento."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar" style="background:#FFEDD5">🧔</div>
                    <div>
                        <div class="testimonial-name">Roberto Lima</div>
                        <div class="testimonial-role">Sócio — Banho & Tosa PetCare, RJ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══ CTA FINAL ══ -->
<section class="cta-final">
    <div class="container cta-final-inner">
        <h2>Pronto para organizar<br>seu pet shop?</h2>
        <p>Comece agora mesmo, sem cartão de crédito. Configure em minutos e veja a diferença no dia a dia.</p>
        <a href="public/register.php" class="btn btn-orange btn-lg">Criar conta grátis</a>
        <p class="cta-trust">✓ Grátis para começar &nbsp;·&nbsp; ✓ Sem burocracia &nbsp;·&nbsp; ✓ Cancele quando quiser</p>
    </div>
</section>

<!-- ══ FOOTER ══ -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="#">
                    <img src="public/img/dark-horizontal.png" alt="Pawfy" style="height: 130px;display:block;width: 242px;">
                </a>
                <p>Sistema de gestão completo para pet shops. Agenda, vendas, estoque e financeiro em um único lugar.</p>
                <div class="footer-social">
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="Facebook">📘</a>
                    <a href="#" title="WhatsApp">💬</a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Sistema</h4>
                <ul>
                    <li><a href="#beneficios">Funcionalidades</a></li>
                    <li><a href="#planos">Planos</a></li>
                    <li><a href="#demo">Demonstração</a></li>
                    <li><a href="public/login.php">Entrar</a></li>
                    <li><a href="public/register.php">Criar conta</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Empresa</h4>
                <ul>
                    <li><a href="#">Sobre o Pawfy</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Parceiros</a></li>
                    <li><a href="#">Contato</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Termos de Uso</a></li>
                    <li><a href="#">Política de Privacidade</a></li>
                    <li><a href="#">Cookies</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> Pawfy. Todos os direitos reservados.</p>
            <div class="footer-bottom-links">
                <a href="#">Termos de Uso</a>
                <a href="#">Privacidade</a>
                <a href="#">Contato</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
