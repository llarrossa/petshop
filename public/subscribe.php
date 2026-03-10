<?php
/**
 * Inicia uma assinatura Stripe via Checkout Session
 * POST: plano (banho_tosa | loja | completo)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: planos.php');
    exit;
}

$plano = sanitize($_POST['plano'] ?? '');

if (!array_key_exists($plano, PLANOS)) {
    $_SESSION['error'] = 'Plano inválido.';
    header('Location: planos.php');
    exit;
}

$priceId = STRIPE_PRICE_IDS[$plano] ?? '';
if (empty($priceId)) {
    $_SESSION['error'] = 'Price ID não configurado para este plano. Verifique config.local.php.';
    header('Location: planos.php');
    exit;
}

if (empty(STRIPE_SECRET_KEY)) {
    $_SESSION['error'] = 'Chave da Stripe não configurada. Verifique config.local.php.';
    header('Location: planos.php');
    exit;
}

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$db        = Database::getInstance();
$companyId = getCompanyId();
$company   = $db->queryOne("SELECT * FROM companies WHERE id = :id", [':id' => $companyId]);

if (!$company) {
    $_SESSION['error'] = 'Empresa não encontrada.';
    header('Location: planos.php');
    exit;
}

try {
    // Criar ou reutilizar Customer na Stripe
    if (!empty($company['stripe_customer_id'])) {
        $customerId = $company['stripe_customer_id'];
    } else {
        $customer = \Stripe\Customer::create([
            'name'  => $company['nome'],
            'email' => $_SESSION['user_email'],
            'metadata' => ['company_id' => (string) $companyId],
        ]);
        $customerId = $customer->id;

        $db->execute(
            "UPDATE companies SET stripe_customer_id = :sid WHERE id = :id",
            [':sid' => $customerId, ':id' => $companyId]
        );
    }

    // Criar Checkout Session
    $checkoutSession = \Stripe\Checkout\Session::create([
        'customer'             => $customerId,
        'mode'                 => 'subscription',
        'payment_method_types' => ['card'],
        'line_items'           => [[
            'price'    => $priceId,
            'quantity' => 1,
        ]],
        'success_url' => APP_URL . '/public/subscription-success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => APP_URL . '/public/planos.php?canceled=1',
        'metadata'    => [
            'company_id' => (string) $companyId,
            'plano'      => $plano,
        ],
        'subscription_data' => [
            'metadata' => [
                'company_id' => (string) $companyId,
                'plano'      => $plano,
            ],
        ],
    ]);

    header('Location: ' . $checkoutSession->url);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log('Stripe error: ' . $e->getMessage());
    $_SESSION['error'] = 'Erro ao iniciar checkout: ' . $e->getMessage();
    header('Location: planos.php');
    exit;
}
