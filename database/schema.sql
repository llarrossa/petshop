-- ====================================
-- SISTEMA SAAS PARA GESTÃO DE PET SHOPS
-- Banco de Dados - Multi-tenant
-- ====================================

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS petshop_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE petshop_saas;

-- ====================================
-- TABELA: companies (Empresas)
-- ====================================
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cnpj VARCHAR(18),
    plano ENUM('banho_tosa', 'loja', 'completo') NOT NULL DEFAULT 'completo',
    status ENUM('ativo', 'inativo', 'suspenso') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_plano (plano),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: users (Usuários do sistema)
-- ====================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin', 'gerente', 'atendente', 'veterinario') DEFAULT 'atendente',
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_email_company (email, company_id),
    INDEX idx_company (company_id),
    INDEX idx_perfil (perfil)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: tutors (Tutores/Clientes)
-- ====================================
CREATE TABLE IF NOT EXISTS tutors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14),
    telefone VARCHAR(20),
    whatsapp VARCHAR(20),
    email VARCHAR(255),
    endereco TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(10),
    observacoes TEXT,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_nome (nome),
    INDEX idx_telefone (telefone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: pets (Animais de estimação)
-- ====================================
CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    tutor_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    especie ENUM('cachorro', 'gato', 'passaro', 'roedor', 'reptil', 'outro') NOT NULL,
    raca VARCHAR(100),
    sexo ENUM('macho', 'femea') NOT NULL,
    data_nascimento DATE,
    peso DECIMAL(8,2),
    cor VARCHAR(50),
    porte ENUM('mini', 'pequeno', 'medio', 'grande', 'gigante'),
    observacoes TEXT,
    status ENUM('ativo', 'inativo', 'falecido') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_tutor (tutor_id),
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: produtos (Produtos para venda)
-- ====================================
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    sku VARCHAR(50),
    categoria VARCHAR(100),
    preco_venda DECIMAL(10,2) NOT NULL,
    preco_custo DECIMAL(10,2),
    estoque_atual INT DEFAULT 0,
    estoque_minimo INT DEFAULT 0,
    unidade VARCHAR(20) DEFAULT 'UN',
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_nome (nome),
    INDEX idx_sku (sku),
    INDEX idx_estoque (estoque_atual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: servicos (Serviços oferecidos)
-- ====================================
CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    duracao_media INT COMMENT 'Duração em minutos',
    categoria VARCHAR(100),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: profissionais (Funcionários)
-- ====================================
CREATE TABLE IF NOT EXISTS profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    funcao VARCHAR(100),
    telefone VARCHAR(20),
    email VARCHAR(255),
    comissao DECIMAL(5,2) DEFAULT 0 COMMENT 'Percentual de comissão',
    tipo_comissao ENUM('percentual', 'valor_fixo') DEFAULT 'percentual',
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: agenda (Agendamentos)
-- ====================================
CREATE TABLE IF NOT EXISTS agenda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    pet_id INT NOT NULL,
    tutor_id INT NOT NULL,
    servico_id INT NOT NULL,
    profissional_id INT,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    status ENUM('agendado', 'confirmado', 'em_atendimento', 'finalizado', 'cancelado', 'faltou') DEFAULT 'agendado',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_data (data),
    INDEX idx_status (status),
    INDEX idx_profissional (profissional_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: vendas (Vendas realizadas)
-- ====================================
CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    tutor_id INT,
    pet_id INT,
    valor_total DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) DEFAULT 0,
    valor_final DECIMAL(10,2) NOT NULL,
    forma_pagamento ENUM('dinheiro', 'debito', 'credito', 'pix', 'boleto', 'outros') NOT NULL,
    status ENUM('finalizada', 'cancelada') DEFAULT 'finalizada',
    observacoes TEXT,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE SET NULL,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_data (data),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: venda_itens (Itens das vendas)
-- ====================================
CREATE TABLE IF NOT EXISTS venda_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venda_id INT NOT NULL,
    tipo_item ENUM('produto', 'servico') NOT NULL,
    item_id INT NOT NULL COMMENT 'ID do produto ou serviço',
    nome_item VARCHAR(255) NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL,
    preco_total DECIMAL(10,2) NOT NULL,
    profissional_id INT COMMENT 'Para serviços',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE SET NULL,
    INDEX idx_venda (venda_id),
    INDEX idx_tipo_item (tipo_item, item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: estoque_movimentacoes (Movimentações de estoque)
-- ====================================
CREATE TABLE IF NOT EXISTS estoque_movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    produto_id INT NOT NULL,
    tipo ENUM('entrada', 'saida', 'ajuste') NOT NULL,
    quantidade INT NOT NULL,
    motivo VARCHAR(255),
    observacoes TEXT,
    usuario_id INT,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_produto (produto_id),
    INDEX idx_tipo (tipo),
    INDEX idx_data (data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: financeiro (Controle financeiro)
-- ====================================
CREATE TABLE IF NOT EXISTS financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    tipo ENUM('receita', 'despesa') NOT NULL,
    categoria VARCHAR(100),
    descricao TEXT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    forma_pagamento ENUM('dinheiro', 'debito', 'credito', 'pix', 'boleto', 'transferencia', 'outros'),
    data_vencimento DATE,
    data_pagamento DATE,
    status ENUM('pendente', 'pago', 'atrasado', 'cancelado') DEFAULT 'pendente',
    venda_id INT COMMENT 'Relaciona com venda se for receita',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_tipo (tipo),
    INDEX idx_status (status),
    INDEX idx_data_vencimento (data_vencimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- TABELA: prontuario (Prontuário veterinário)
-- ====================================
CREATE TABLE IF NOT EXISTS prontuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    pet_id INT NOT NULL,
    tutor_id INT NOT NULL,
    veterinario_id INT,
    tipo ENUM('consulta', 'vacina', 'exame', 'cirurgia', 'outros') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    peso_atual DECIMAL(8,2),
    temperatura DECIMAL(4,2),
    prescricao TEXT,
    retorno DATE,
    arquivos TEXT COMMENT 'JSON com caminhos de arquivos anexados',
    data_atendimento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    FOREIGN KEY (veterinario_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_pet (pet_id),
    INDEX idx_data (data_atendimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- DADOS INICIAIS PARA TESTES
-- ====================================

-- Inserir empresa de exemplo
INSERT INTO companies (nome, cnpj, plano, status) VALUES
('Pet Shop Exemplo', '12.345.678/0001-90', 'completo', 'ativo');

-- Inserir usuário administrador (senha: admin123)
INSERT INTO users (company_id, nome, email, senha, perfil, status) VALUES
(1, 'Administrador', 'admin@petshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ativo');

-- Inserir alguns tutores de exemplo
INSERT INTO tutors (company_id, nome, telefone, whatsapp, email) VALUES
(1, 'João Silva', '(11) 98765-4321', '(11) 98765-4321', 'joao@email.com'),
(1, 'Maria Santos', '(11) 91234-5678', '(11) 91234-5678', 'maria@email.com');

-- Inserir alguns pets de exemplo
INSERT INTO pets (company_id, tutor_id, nome, especie, raca, sexo, data_nascimento, peso) VALUES
(1, 1, 'Rex', 'cachorro', 'Labrador', 'macho', '2020-05-15', 25.5),
(1, 1, 'Nina', 'cachorro', 'Poodle', 'femea', '2021-03-20', 8.3),
(1, 2, 'Mimi', 'gato', 'Siamês', 'femea', '2019-11-10', 4.2);

-- Inserir alguns serviços de exemplo
INSERT INTO servicos (company_id, nome, preco, duracao_media, categoria) VALUES
(1, 'Banho (Porte Pequeno)', 45.00, 60, 'Banho'),
(1, 'Banho (Porte Médio)', 60.00, 75, 'Banho'),
(1, 'Banho (Porte Grande)', 80.00, 90, 'Banho'),
(1, 'Tosa Higiênica', 35.00, 30, 'Tosa'),
(1, 'Tosa Completa', 70.00, 90, 'Tosa'),
(1, 'Consulta Veterinária', 120.00, 30, 'Veterinária');

-- Inserir alguns produtos de exemplo
INSERT INTO produtos (company_id, nome, sku, preco_venda, preco_custo, estoque_atual, estoque_minimo, categoria) VALUES
(1, 'Ração Premium 15kg', 'RAC-PREM-15', 189.90, 130.00, 50, 10, 'Alimentação'),
(1, 'Shampoo Antipulgas', 'SHP-ANTI-500', 45.90, 28.00, 30, 5, 'Higiene'),
(1, 'Coleira Antipulgas', 'COL-ANTI-M', 79.90, 45.00, 20, 5, 'Acessórios'),
(1, 'Brinquedo Mordedor', 'BRI-MORD-01', 25.90, 12.00, 15, 5, 'Brinquedos');

-- Inserir alguns profissionais de exemplo
INSERT INTO profissionais (company_id, nome, funcao, comissao) VALUES
(1, 'Ana Paula', 'Tosadora', 15.00),
(1, 'Carlos Eduardo', 'Banhista', 10.00),
(1, 'Dr. Roberto', 'Veterinário', 20.00);
