<?php
/**
 * Callback de sucesso após o pagamento na Stripe
 * Stripe redireciona para cá com ?session_id={CHECKOUT_SESSION_ID}
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

verificarLogin();

$sessionId = sanitize($_GET['session_id'] ?? '');

if (empty($sessionId) || empty(STRIPE_SECRET_KEY)) {
    header('Location: ' . APP_URL . '/public/planos.php');
    exit;
}

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    $checkoutSession = \Stripe\Checkout\Session::retrieve([
        'id'     => $sessionId,
        'expand' => ['subscription'],
    ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log('Stripe error: ' . $e->getMessage());
    $_SESSION['error'] = 'Não foi possível verificar o pagamento. Entre em contato com o suporte.';
    header('Location: ' . APP_URL . '/public/planos.php');
    exit;
}

// Verificar se o checkout foi concluído
if ($checkoutSession->status !== 'complete') {
    header('Location: ' . APP_URL . '/public/planos.php?canceled=1');
    exit;
}

$subscription = $checkoutSession->subscription;
$companyId    = getCompanyId();
$plano        = $subscription->metadata['plano']
             ?? $checkoutSession->metadata['plano']
             ?? $_SESSION['plano']
             ?? 'completo';

$db = Database::getInstance();

// Atualizar empresa com dados da assinatura
$db->execute(
    "UPDATE companies
        SET stripe_subscription_id = :sub_id,
            subscription_status    = :status,
            plano                  = :plano
      WHERE id = :id",
    [
        ':sub_id' => $subscription->id,
        ':status' => $subscription->status,
        ':plano'  => $plano,
        ':id'     => $companyId,
    ]
);

// Atualizar sessão
$_SESSION['subscription_status'] = $subscription->status;
$_SESSION['plano']               = $plano;

$_SESSION['success'] = 'Assinatura ativada com sucesso! Bem-vindo ao Pawfy. 🎉';
header('Location: ' . APP_URL . '/index.php?page=dashboard');
exit;
