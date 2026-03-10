<?php
/**
 * Arquivo de Configuração do Sistema
 * Pet Shop SaaS - Multi-tenant
 */

// Carregar configurações locais (não versionadas)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Configurações do Banco de Dados
define('DB_HOST', DB_HOST_LOCAL ?? 'localhost');
define('DB_NAME', DB_NAME_LOCAL ?? 'petshop_saas');
define('DB_USER', DB_USER_LOCAL ?? 'root');
define('DB_PASS', DB_PASS_LOCAL ?? '');
define('DB_CHARSET', 'utf8mb4');

// Configurações da Aplicação
define('APP_NAME', 'Pawfy');
define('APP_VERSION', '1.0.0');
define('APP_URL', APP_URL_LOCAL ?? 'http://localhost/petshop');

// Configurações da Stripe
define('STRIPE_SECRET_KEY',      STRIPE_SECRET_KEY_LOCAL      ?? '');
define('STRIPE_PUBLISHABLE_KEY', STRIPE_PUBLISHABLE_KEY_LOCAL ?? '');
define('STRIPE_WEBHOOK_SECRET',  STRIPE_WEBHOOK_SECRET_LOCAL  ?? '');

// Price IDs dos planos na Stripe
define('STRIPE_PRICE_BANHO_TOSA', STRIPE_PRICE_BANHO_TOSA_LOCAL ?? '');
define('STRIPE_PRICE_LOJA',       STRIPE_PRICE_LOJA_LOCAL       ?? '');
define('STRIPE_PRICE_COMPLETO',   STRIPE_PRICE_COMPLETO_LOCAL   ?? '');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Sessão
$sessionsPath = __DIR__ . '/../sessions';
if (!is_dir($sessionsPath)) {
    mkdir($sessionsPath, 0777, true);
}
ini_set('session.save_path', $sessionsPath);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mudar para 1 em produção com HTTPS

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações de Erro (Desabilitar em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações de Upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Configurações de Paginação
define('ITEMS_PER_PAGE', 20);

// Planos disponíveis e seus módulos
define('PLANOS', [
    'banho_tosa' => [
        'nome' => 'Banho & Tosa',
        'modulos' => ['agenda', 'tutores', 'pets', 'servicos', 'profissionais', 'dashboard']
    ],
    'loja' => [
        'nome' => 'Pet Shop',
        'modulos' => ['produtos', 'vendas', 'estoque', 'financeiro', 'relatorios', 'dashboard']
    ],
    'completo' => [
        'nome' => 'Completo',
        'modulos' => ['agenda', 'tutores', 'pets', 'servicos', 'profissionais', 'produtos', 'vendas', 'estoque', 'financeiro', 'relatorios', 'prontuario', 'dashboard']
    ]
]);

// Mapeamento de plano para Stripe Price ID
define('STRIPE_PRICE_IDS', [
    'banho_tosa' => STRIPE_PRICE_BANHO_TOSA,
    'loja'       => STRIPE_PRICE_LOJA,
    'completo'   => STRIPE_PRICE_COMPLETO,
]);

// Statuses de assinatura considerados ativos
define('SUBSCRIPTION_ACTIVE_STATUSES', ['active', 'trialing']);

// Função auxiliar para verificar se usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . APP_URL . '/public/login.php');
        exit;
    }
}

// Verifica se a assinatura da empresa está ativa
function assinaturaAtiva() {
    $status = $_SESSION['subscription_status'] ?? 'incomplete';
    return in_array($status, SUBSCRIPTION_ACTIVE_STATUSES);
}

// Função auxiliar para obter company_id do usuário logado
function getCompanyId() {
    return $_SESSION['company_id'] ?? null;
}

// Função auxiliar para verificar se módulo está disponível no plano
function moduloDisponivel($modulo) {
    $plano = $_SESSION['plano'] ?? 'completo';
    return in_array($modulo, PLANOS[$plano]['modulos']);
}

// Função para sanitizar entrada de dados
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Função para formatar moeda
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

// Função para formatar data BR
function formatarData($data) {
    if (empty($data)) return '';
    $timestamp = strtotime($data);
    return date('d/m/Y', $timestamp);
}

// Função para formatar data e hora BR
function formatarDataHora($data) {
    if (empty($data)) return '';
    $timestamp = strtotime($data);
    return date('d/m/Y H:i', $timestamp);
}

// Função para converter data BR para MySQL
function dataBRparaMySQL($data) {
    if (empty($data)) return null;
    $partes = explode('/', $data);
    if (count($partes) == 3) {
        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    }
    return null;
}

// Função para gerar cabeçalho de tabela ordenável
function thSort($label, $col, $cur_col, $cur_dir) {
    $params = $_GET;
    $params['orderby'] = $col;
    $params['order'] = ($cur_col === $col && $cur_dir === 'asc') ? 'desc' : 'asc';
    $icon = $cur_col === $col ? ($cur_dir === 'asc' ? ' ▲' : ' ▼') : '';
    return '<a href="?' . htmlspecialchars(http_build_query($params)) . '" class="sort-link">'
        . htmlspecialchars($label) . $icon . '</a>';
}
