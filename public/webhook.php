<?php
/**
 * Webhook Handler — Stripe
 *
 * URL a cadastrar no Dashboard da Stripe:
 *   https://seudominio.com/public/webhook.php
 *
 * Eventos tratados:
 *   - checkout.session.completed
 *   - customer.subscription.updated
 *   - customer.subscription.deleted
 *   - invoice.payment_failed
 *   - invoice.paid
 */

// Webhook não usa sessão nem autenticação normal
define('WEBHOOK_REQUEST', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$payload   = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// Verificar assinatura do webhook
try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        STRIPE_WEBHOOK_SECRET
    );
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit;
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit;
}

$db = Database::getInstance();

// ─── Helpers ────────────────────────────────────────────────────────────────

function atualizarStatusPorSubscriptionId(Database $db, string $subscriptionId, string $status): void {
    $db->execute(
        "UPDATE companies SET subscription_status = :status WHERE stripe_subscription_id = :sub_id",
        [':status' => $status, ':sub_id' => $subscriptionId]
    );
}

function atualizarStatusPorCustomerId(Database $db, string $customerId, string $status): void {
    $db->execute(
        "UPDATE companies SET subscription_status = :status WHERE stripe_customer_id = :cid",
        [':status' => $status, ':cid' => $customerId]
    );
}

// ─── Roteamento de eventos ───────────────────────────────────────────────────

switch ($event->type) {

    /**
     * Checkout concluído — assinatura criada.
     * (Garante atualização mesmo que subscription-success.php falhe)
     */
    case 'checkout.session.completed':
        $session = $event->data->object;

        if ($session->mode !== 'subscription') break;

        $companyId = (int) ($session->metadata->company_id ?? 0);
        $plano     = $session->metadata->plano ?? null;

        if (!$companyId || !$plano) break;

        $subscriptionId = is_string($session->subscription)
            ? $session->subscription
            : ($session->subscription->id ?? null);

        if (!$subscriptionId) break;

        try {
            $sub = \Stripe\Subscription::retrieve($subscriptionId);
            $db->execute(
                "UPDATE companies
                    SET stripe_subscription_id = :sub_id,
                        subscription_status    = :status,
                        plano                  = :plano
                  WHERE id = :id",
                [
                    ':sub_id' => $sub->id,
                    ':status' => $sub->status,
                    ':plano'  => $plano,
                    ':id'     => $companyId,
                ]
            );
        } catch (\Exception $e) {
            error_log('Webhook checkout.session.completed error: ' . $e->getMessage());
        }
        break;

    /**
     * Assinatura atualizada (mudança de plano, renovação, pausada, etc.)
     */
    case 'customer.subscription.updated':
        $sub   = $event->data->object;
        $plano = $sub->metadata->plano ?? null;

        if ($plano) {
            $db->execute(
                "UPDATE companies
                    SET subscription_status = :status,
                        plano               = :plano
                  WHERE stripe_subscription_id = :sub_id",
                [':status' => $sub->status, ':plano' => $plano, ':sub_id' => $sub->id]
            );
        } else {
            atualizarStatusPorSubscriptionId($db, $sub->id, $sub->status);
        }
        break;

    /**
     * Assinatura cancelada ou expirada.
     */
    case 'customer.subscription.deleted':
        $sub = $event->data->object;
        atualizarStatusPorSubscriptionId($db, $sub->id, 'canceled');
        break;

    /**
     * Fatura paga com sucesso (renovação).
     */
    case 'invoice.paid':
        $invoice = $event->data->object;
        if (!empty($invoice->subscription)) {
            atualizarStatusPorSubscriptionId(
                $db,
                is_string($invoice->subscription) ? $invoice->subscription : $invoice->subscription->id,
                'active'
            );
        }
        break;

    /**
     * Falha no pagamento da fatura.
     */
    case 'invoice.payment_failed':
        $invoice = $event->data->object;
        atualizarStatusPorCustomerId($db, $invoice->customer, 'past_due');
        break;
}

http_response_code(200);
echo json_encode(['status' => 'ok']);
