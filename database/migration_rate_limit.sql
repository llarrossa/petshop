-- Migration: Tabela de rate limiting para login e reset de senha
-- Executar: mysql -u root -p petshop_saas < database/migration_rate_limit.sql

CREATE TABLE IF NOT EXISTS rate_limits (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    identifier   VARCHAR(128) NOT NULL COMMENT 'IP + ação (hash)',
    action       VARCHAR(64)  NOT NULL DEFAULT 'login',
    attempts     INT          NOT NULL DEFAULT 1,
    window_start DATETIME     NOT NULL,
    INDEX idx_identifier_action (identifier, action),
    INDEX idx_window_start (window_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
