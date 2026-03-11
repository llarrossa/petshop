-- Migração: Trial gratuito de 7 dias
-- Adiciona coluna trial_ends_at na tabela companies

ALTER TABLE companies
    ADD COLUMN trial_ends_at DATETIME NULL AFTER subscription_status;
