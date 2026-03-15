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

// Configurações de SMTP
define('SMTP_HOST',       SMTP_HOST_LOCAL       ?? 'smtp.gmail.com');
define('SMTP_PORT',       SMTP_PORT_LOCAL       ?? 587);
define('SMTP_ENCRYPTION', SMTP_ENCRYPTION_LOCAL ?? 'tls');   // 'tls' ou 'ssl'
define('SMTP_USER',       SMTP_USER_LOCAL       ?? '');
define('SMTP_PASS',       SMTP_PASS_LOCAL       ?? '');
define('SMTP_FROM_EMAIL', SMTP_FROM_EMAIL_LOCAL ?? '');
define('SMTP_FROM_NAME',  SMTP_FROM_NAME_LOCAL  ?? APP_NAME);

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

// Configurações de Erro (display_errors desabilitado fora de ambiente local)
error_reporting(E_ALL);
$_localIps = ['127.0.0.1', '::1', ''];
ini_set('display_errors', in_array($_SERVER['REMOTE_ADDR'] ?? '', $_localIps) ? 1 : 0);
unset($_localIps);

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
        'modulos' => ['agenda', 'clientes', 'pets', 'servicos', 'profissionais', 'prontuario', 'nota_fiscal', 'dashboard']
    ],
    'loja' => [
        'nome' => 'Pet Shop',
        'modulos' => ['produtos', 'vendas', 'estoque', 'financeiro', 'relatorios', 'nota_fiscal', 'dashboard']
    ],
    'completo' => [
        'nome' => 'Completo',
        'modulos' => ['agenda', 'clientes', 'pets', 'servicos', 'profissionais', 'produtos', 'vendas', 'estoque', 'financeiro', 'relatorios', 'prontuario', 'nota_fiscal', 'dashboard']
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

// Retorna os dias restantes do trial (0 se expirado ou sem trial)
function diasRestantesTrial() {
    $trialEndsAt = $_SESSION['trial_ends_at'] ?? null;
    if (!$trialEndsAt) return 0;
    $diff = strtotime($trialEndsAt) - time();
    return max(0, (int) ceil($diff / 86400));
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

// Função para gerar cabeçalho de tabela ordenável
function thSort($label, $col, $cur_col, $cur_dir) {
    $params = $_GET;
    $params['orderby'] = $col;
    $params['order'] = ($cur_col === $col && $cur_dir === 'asc') ? 'desc' : 'asc';
    $icon = $cur_col === $col ? ($cur_dir === 'asc' ? ' ▲' : ' ▼') : '';
    return '<a href="?' . htmlspecialchars(http_build_query($params)) . '" class="sort-link">'
        . htmlspecialchars($label) . $icon . '</a>';
}

// Gera e retorna o CSRF token da sessão atual
function getCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Valida o CSRF token enviado no POST; aborta com 403 se inválido
function validateCsrfToken() {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        $_SESSION['error'] = 'Token de segurança inválido. Recarregue a página e tente novamente.';
        $ref = $_SERVER['HTTP_REFERER'] ?? '?page=dashboard';
        header('Location: ' . safeReturnUrl($ref, '?page=dashboard'));
        exit;
    }
}

// Valida que uma URL de retorno é relativa (começa com ?)
function safeReturnUrl($url, $default = '?page=dashboard') {
    $url = trim($url ?? '');
    if ($url === '' || $url[0] !== '?') {
        return $default;
    }
    return $url;
}
