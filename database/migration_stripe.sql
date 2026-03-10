-- ====================================
-- MIGRAÇÃO: Integração Stripe
-- Adiciona campos de assinatura na tabela companies
-- ====================================

ALTER TABLE companies
    ADD COLUMN stripe_customer_id     VARCHAR(50)  NULL                        AFTER cnpj,
    ADD COLUMN stripe_subscription_id VARCHAR(50)  NULL                        AFTER stripe_customer_id,
    ADD COLUMN subscription_status    ENUM(
        'incomplete',
        'incomplete_expired',
        'trialing',
        'active',
        'past_due',
        'canceled',
        'unpaid',
        'paused'
    ) NOT NULL DEFAULT 'incomplete'                                             AFTER stripe_subscription_id,
    ADD INDEX idx_stripe_customer     (stripe_customer_id),
    ADD INDEX idx_subscription_status (subscription_status);
