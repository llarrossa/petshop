-- ================================================
-- Migração: Criação da tabela prontuarios
-- Sistema Pawfy - Pet Shop SaaS
-- ================================================

CREATE TABLE IF NOT EXISTS prontuarios (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id       INT UNSIGNED NOT NULL,
    pet_id           INT UNSIGNED NOT NULL,
    cliente_id       INT UNSIGNED NOT NULL,
    profissional_id  INT UNSIGNED DEFAULT NULL,
    data_atendimento DATE NOT NULL,
    peso             DECIMAL(5,2) DEFAULT NULL COMMENT 'Peso do pet em kg no atendimento',
    observacoes      TEXT DEFAULT NULL,
    recomendacoes    TEXT DEFAULT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_company   (company_id),
    INDEX idx_pet       (pet_id),
    INDEX idx_cliente   (cliente_id),
    INDEX idx_data      (data_atendimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
