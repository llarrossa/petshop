<?php
/**
 * Página Inicial do Sistema - Router
 */

require_once __DIR__ . '/config/config.php';

// Usuário não logado: exibir landing page
if (!isset($_SESSION['user_id'])) {
    include __DIR__ . '/landing.php';
    exit;
}


// Troca de plano (modo teste)
if (isset($_GET['trocar_plano'])) {
    $plano = $_GET['trocar_plano'];
    if (array_key_exists($plano, PLANOS)) {
        $_SESSION['plano'] = $plano;
    }
    header('Location: ?page=dashboard');
    exit;
}

// Determinar qual página/controller carregar
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'list';

// Mapear páginas para controllers
$controllers = [
    'dashboard' => __DIR__ . '/public/dashboard.php',
    'tutores' => __DIR__ . '/controllers/tutores.php',
    'pets' => __DIR__ . '/controllers/pets.php',
    'produtos' => __DIR__ . '/controllers/produtos.php',
    'servicos' => __DIR__ . '/controllers/servicos.php',
    'profissionais' => __DIR__ . '/controllers/profissionais.php',
    'agenda' => __DIR__ . '/controllers/agenda.php',
    'vendas' => __DIR__ . '/controllers/vendas.php',
    'financeiro' => __DIR__ . '/controllers/financeiro.php',
    'relatorios' => __DIR__ . '/controllers/relatorios.php',
];

// Verificar se a página existe
if (isset($controllers[$page]) && file_exists($controllers[$page])) {
    include $controllers[$page];
} else {
    // Página não encontrada
    $_SESSION['error'] = 'Página não encontrada.';
    header('Location: ?page=dashboard');
    exit;
}
