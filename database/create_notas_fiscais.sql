-- ================================================
-- Migração: Módulo Nota Fiscal (NFS-e)
-- Sistema Pawfy - Pet Shop SaaS
-- ================================================

-- Tabela principal de notas fiscais
CREATE TABLE IF NOT EXISTS notas_fiscais (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id          INT UNSIGNED NOT NULL,
    venda_id            INT UNSIGNED DEFAULT NULL,
    cliente_id          INT UNSIGNED DEFAULT NULL,
    ref_externa         VARCHAR(60)  DEFAULT NULL  COMMENT 'Referência única enviada ao provedor NFS-e',
    numero_nota         VARCHAR(30)  DEFAULT NULL,
    codigo_verificacao  VARCHAR(100) DEFAULT NULL,
    status              ENUM('pendente','processando','emitida','erro','cancelada') NOT NULL DEFAULT 'pendente',
    valor               DECIMAL(10,2) DEFAULT NULL,
    descricao_servico   TEXT         DEFAULT NULL,
    data_emissao        DATE         DEFAULT NULL,
    pdf_url             VARCHAR(600) DEFAULT NULL,
    resposta_api        TEXT         DEFAULT NULL  COMMENT 'JSON completo retornado pela API',
    mensagem_erro       TEXT         DEFAULT NULL,
    created_at          TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_company  (company_id),
    INDEX idx_venda    (venda_id),
    INDEX idx_status   (status),
    INDEX idx_ref      (ref_externa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configurações fiscais por empresa
CREATE TABLE IF NOT EXISTS config_fiscal (
    id                          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id                  INT UNSIGNED NOT NULL UNIQUE,
    cnpj                        VARCHAR(18)  DEFAULT NULL,
    razao_social                VARCHAR(200) DEFAULT NULL,
    inscricao_municipal         VARCHAR(50)  DEFAULT NULL,
    logradouro                  VARCHAR(200) DEFAULT NULL,
    numero_endereco             VARCHAR(20)  DEFAULT NULL,
    complemento                 VARCHAR(100) DEFAULT NULL,
    bairro                      VARCHAR(100) DEFAULT NULL,
    codigo_municipio            VARCHAR(20)  DEFAULT NULL  COMMENT 'Código IBGE do município',
    municipio                   VARCHAR(100) DEFAULT NULL,
    uf                          CHAR(2)      DEFAULT NULL,
    cep                         VARCHAR(10)  DEFAULT NULL,
    codigo_servico              VARCHAR(20)  DEFAULT NULL  COMMENT 'Item lista serviço LC 116/2003 (ex: 14.01)',
    codigo_tributario_municipio VARCHAR(20)  DEFAULT NULL,
    aliquota_iss                DECIMAL(5,4) DEFAULT 0.0500,
    nfse_api_token              VARCHAR(600) DEFAULT NULL  COMMENT 'Token de API do provedor NFS-e (por empresa)',
    nfse_ambiente               ENUM('homologacao','producao') DEFAULT 'homologacao',
    nfse_provedor               VARCHAR(50)  DEFAULT 'focusnfe',
    created_at                  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at                  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_company (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indicador de NF na tabela de vendas
ALTER TABLE vendas
    ADD COLUMN IF NOT EXISTS tem_nota_fiscal TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
    AFTER status;
