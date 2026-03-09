-- ====================================
-- SEED DE DADOS PARA TESTES
-- Pet Shop SaaS - company_id = 1
-- ====================================

USE petshop_saas;

-- ====================================
-- TUTORES (adiciona 18 para chegar a 20)
-- ====================================
INSERT INTO tutors (company_id, nome, cpf, telefone, whatsapp, email, endereco, cidade, estado, cep) VALUES
(1, 'Carlos Oliveira',     '123.456.789-01', '(11) 97001-0001', '(11) 97001-0001', 'carlos.oliveira@email.com',   'Rua das Flores, 101',      'São Paulo', 'SP', '01001-000'),
(1, 'Ana Beatriz Costa',   '234.567.890-02', '(11) 97001-0002', '(11) 97001-0002', 'ana.costa@email.com',         'Av. Paulista, 202',        'São Paulo', 'SP', '01310-100'),
(1, 'Roberto Ferreira',    '345.678.901-03', '(11) 97001-0003', '(11) 97001-0003', 'roberto.f@email.com',         'Rua Augusta, 303',         'São Paulo', 'SP', '01305-000'),
(1, 'Fernanda Lima',       '456.789.012-04', '(11) 97001-0004', '(11) 97001-0004', 'fernanda.lima@email.com',     'Rua Oscar Freire, 404',    'São Paulo', 'SP', '01426-001'),
(1, 'Marcos Souza',        '567.890.123-05', '(11) 97001-0005', '(11) 97001-0005', 'marcos.souza@email.com',      'Rua Consolação, 505',      'São Paulo', 'SP', '01301-000'),
(1, 'Juliana Pereira',     '678.901.234-06', '(11) 97001-0006', '(11) 97001-0006', 'juliana.p@email.com',         'Rua Haddock Lobo, 606',    'São Paulo', 'SP', '01414-001'),
(1, 'Ricardo Alves',       '789.012.345-07', '(11) 97001-0007', '(11) 97001-0007', 'ricardo.alves@email.com',     'Av. Brasil, 707',          'São Paulo', 'SP', '01431-000'),
(1, 'Patrícia Carvalho',   '890.123.456-08', '(11) 97001-0008', '(11) 97001-0008', 'patricia.c@email.com',        'Rua Pamplona, 808',        'São Paulo', 'SP', '01405-001'),
(1, 'Eduardo Martins',     '901.234.567-09', '(11) 97001-0009', '(11) 97001-0009', 'eduardo.m@email.com',         'Rua da Consolação, 909',   'São Paulo', 'SP', '01302-000'),
(1, 'Camila Rodrigues',    '012.345.678-10', '(11) 97001-0010', '(11) 97001-0010', 'camila.r@email.com',          'Av. Rebouças, 1010',       'São Paulo', 'SP', '05402-000'),
(1, 'Thiago Nascimento',   '111.222.333-11', '(11) 97001-0011', '(11) 97001-0011', 'thiago.n@email.com',          'Rua Bela Cintra, 1111',    'São Paulo', 'SP', '01415-000'),
(1, 'Letícia Barbosa',     '222.333.444-12', '(11) 97001-0012', '(11) 97001-0012', 'leticia.b@email.com',         'Rua Frei Caneca, 1212',    'São Paulo', 'SP', '01307-001'),
(1, 'Gabriel Teixeira',    '333.444.555-13', '(11) 97001-0013', '(11) 97001-0013', 'gabriel.t@email.com',         'Rua Sergipe, 1313',        'São Paulo', 'SP', '01243-000'),
(1, 'Amanda Gomes',        '444.555.666-14', '(11) 97001-0014', '(11) 97001-0014', 'amanda.g@email.com',          'Rua Itapeva, 1414',        'São Paulo', 'SP', '01332-000'),
(1, 'Bruno Araújo',        '555.666.777-15', '(11) 97001-0015', '(11) 97001-0015', 'bruno.araujo@email.com',      'Rua da Glória, 1515',      'São Paulo', 'SP', '01510-000'),
(1, 'Vanessa Castro',      '666.777.888-16', '(11) 97001-0016', '(11) 97001-0016', 'vanessa.c@email.com',         'Rua Vergueiro, 1616',      'São Paulo', 'SP', '01504-000'),
(1, 'Felipe Mendes',       '777.888.999-17', '(11) 97001-0017', '(11) 97001-0017', 'felipe.m@email.com',          'Av. Domingos de Morais, 1717', 'São Paulo', 'SP', '04010-000'),
(1, 'Isabela Moreira',     '888.999.000-18', '(11) 97001-0018', '(11) 97001-0018', 'isabela.m@email.com',         'Rua Funchal, 1818',        'São Paulo', 'SP', '04551-060');

-- ====================================
-- PETS (adiciona 17 para chegar a 20)
-- ====================================
INSERT INTO pets (company_id, tutor_id, nome, especie, raca, sexo, data_nascimento, peso, cor, porte) VALUES
(1, 3,  'Bolt',     'cachorro', 'Golden Retriever', 'macho',  '2021-07-10', 32.0, 'Dourado',   'grande'),
(1, 3,  'Luna',     'gato',     'Persa',            'femea',  '2022-01-15', 3.8,  'Branco',    NULL),
(1, 4,  'Thor',     'cachorro', 'Rottweiler',       'macho',  '2020-11-20', 45.0, 'Preto',     'gigante'),
(1, 5,  'Mel',      'cachorro', 'Shih Tzu',         'femea',  '2022-04-05', 5.5,  'Caramelo',  'pequeno'),
(1, 5,  'Bob',      'cachorro', 'Bulldog Francês',  'macho',  '2021-09-12', 12.0, 'Malhado',   'pequeno'),
(1, 6,  'Simba',    'gato',     'Maine Coon',       'macho',  '2020-06-18', 6.5,  'Laranja',   NULL),
(1, 7,  'Bella',    'cachorro', 'Beagle',           'femea',  '2021-02-28', 14.0, 'Tricolor',  'medio'),
(1, 7,  'Max',      'cachorro', 'Pastor Alemão',    'macho',  '2019-08-03', 38.0, 'Preto/Bege','grande'),
(1, 8,  'Nala',     'gato',     'Ragdoll',          'femea',  '2022-03-22', 4.1,  'Cinza/Bco', NULL),
(1, 9,  'Duque',    'cachorro', 'Dachshund',        'macho',  '2021-12-01', 9.0,  'Marrom',    'pequeno'),
(1, 10, 'Lola',     'cachorro', 'Yorkshire',        'femea',  '2022-07-14', 3.2,  'Castanho',  'mini'),
(1, 11, 'Zeus',     'cachorro', 'Husky Siberiano',  'macho',  '2020-04-25', 28.0, 'Cinza/Bco', 'medio'),
(1, 12, 'Mia',      'gato',     'Bengala',          'femea',  '2021-10-08', 4.8,  'Malhado',   NULL),
(1, 13, 'Apolo',    'cachorro', 'Boxer',            'macho',  '2020-01-30', 34.0, 'Caramelo',  'grande'),
(1, 14, 'Pipoca',   'cachorro', 'Maltês',           'femea',  '2022-08-20', 2.9,  'Branco',    'mini'),
(1, 15, 'Caramelo', 'cachorro', 'Vira-lata',        'macho',  '2019-05-11', 18.0, 'Caramelo',  'medio'),
(1, 16, 'Frida',    'gato',     'Angorá',           'femea',  '2021-06-03', 3.5,  'Branco',    NULL);

-- ====================================
-- SERVIÇOS (adiciona 14 para chegar a 20)
-- ====================================
INSERT INTO servicos (company_id, nome, descricao, preco, duracao_media, categoria) VALUES
(1, 'Banho (Porte Mini)',         'Banho para pets mini até 4kg',             35.00,  45,  'Banho'),
(1, 'Banho (Porte Gigante)',      'Banho para pets acima de 40kg',            120.00, 120, 'Banho'),
(1, 'Tosa na Tesoura',            'Tosa artística com tesoura',               90.00,  90,  'Tosa'),
(1, 'Tosa Higiênica Gato',        'Tosa higiênica específica para gatos',     50.00,  45,  'Tosa'),
(1, 'Banho Gato',                 'Banho especializado para gatos',           80.00,  60,  'Banho'),
(1, 'Hidratação',                 'Tratamento de hidratação do pelo',         40.00,  30,  'Estética'),
(1, 'Escovação Dental',           'Higiene bucal profissional',               30.00,  20,  'Saúde'),
(1, 'Limpeza de Ouvido',          'Limpeza e higienização dos ouvidos',       25.00,  15,  'Saúde'),
(1, 'Corte de Unhas',             'Corte e lixamento de unhas',               20.00,  15,  'Saúde'),
(1, 'Antipulgas e Carrapatos',    'Aplicação de produto antipulgas',          45.00,  20,  'Saúde'),
(1, 'Consulta Veterinária Retorno','Retorno de consulta veterinária',         60.00,  20,  'Veterinária'),
(1, 'Vacinação',                  'Aplicação de vacinas',                     80.00,  15,  'Veterinária'),
(1, 'Microchipagem',              'Implante de microchip de identificação',   150.00, 20,  'Veterinária'),
(1, 'Pacote Banho + Tosa',        'Banho completo + tosa higiênica (médio)',  90.00,  120, 'Pacote');

-- ====================================
-- PROFISSIONAIS (adiciona 17 para chegar a 20)
-- ====================================
INSERT INTO profissionais (company_id, nome, funcao, telefone, email, comissao, tipo_comissao) VALUES
(1, 'Beatriz Souza',      'Tosadora',          '(11) 98001-0001', 'beatriz.s@petshop.com',   15.00, 'percentual'),
(1, 'Diego Ramos',        'Banhista',          '(11) 98001-0002', 'diego.r@petshop.com',     10.00, 'percentual'),
(1, 'Priscila Nunes',     'Tosadora',          '(11) 98001-0003', 'priscila.n@petshop.com',  15.00, 'percentual'),
(1, 'Luciano Pinto',      'Banhista',          '(11) 98001-0004', 'luciano.p@petshop.com',   10.00, 'percentual'),
(1, 'Dra. Mariana Lopes', 'Veterinária',       '(11) 98001-0005', 'mariana.l@petshop.com',   20.00, 'percentual'),
(1, 'Dr. Fábio Cardoso',  'Veterinário',       '(11) 98001-0006', 'fabio.c@petshop.com',     20.00, 'percentual'),
(1, 'Renata Vieira',      'Atendente',         '(11) 98001-0007', 'renata.v@petshop.com',     0.00, 'percentual'),
(1, 'Leandro Campos',     'Tosador',           '(11) 98001-0008', 'leandro.c@petshop.com',   15.00, 'percentual'),
(1, 'Tatiane Melo',       'Banhista',          '(11) 98001-0009', 'tatiane.m@petshop.com',   10.00, 'percentual'),
(1, 'Hugo Freitas',       'Auxiliar Vet.',     '(11) 98001-0010', 'hugo.f@petshop.com',       5.00, 'percentual'),
(1, 'Samara Correia',     'Tosadora',          '(11) 98001-0011', 'samara.c@petshop.com',    15.00, 'percentual'),
(1, 'Rafael Cunha',       'Banhista',          '(11) 98001-0012', 'rafael.c@petshop.com',    10.00, 'percentual'),
(1, 'Daniela Serra',      'Recepcionista',     '(11) 98001-0013', 'daniela.s@petshop.com',    0.00, 'percentual'),
(1, 'Alexandre Barros',   'Tosador',           '(11) 98001-0014', 'alexandre.b@petshop.com', 15.00, 'percentual'),
(1, 'Cristina Fonseca',   'Veterinária',       '(11) 98001-0015', 'cristina.f@petshop.com',  20.00, 'percentual'),
(1, 'Maurício Dias',      'Auxiliar Banho',    '(11) 98001-0016', 'mauricio.d@petshop.com',   8.00, 'percentual'),
(1, 'Roberta Assis',      'Tosadora',          '(11) 98001-0017', 'roberta.a@petshop.com',   15.00, 'percentual');

-- ====================================
-- PRODUTOS (adiciona 16 para chegar a 20)
-- ====================================
INSERT INTO produtos (company_id, nome, sku, descricao, preco_venda, preco_custo, estoque_atual, estoque_minimo, unidade, categoria) VALUES
(1, 'Ração Premium Gatos 3kg',    'RAC-GAT-3',    'Ração premium para gatos adultos',         89.90,  55.00, 40, 8,  'UN', 'Alimentação'),
(1, 'Petisco Ossinho',             'PET-OSS-01',   'Petisco sabor frango',                     12.90,   6.00, 80, 15, 'UN', 'Petiscos'),
(1, 'Shampoo Hidratante 500ml',    'SHP-HID-500',  'Shampoo com aloe vera',                    38.90,  22.00, 25, 5,  'UN', 'Higiene'),
(1, 'Condicionador 500ml',         'CON-500',      'Condicionador para pelos longos',           35.90,  20.00, 20, 5,  'UN', 'Higiene'),
(1, 'Areia Sanitária 4kg',         'ARE-SAN-4',    'Areia higiênica para gatos',               29.90,  15.00, 35, 10, 'UN', 'Higiene'),
(1, 'Tapete Higiênico 30un',       'TAP-HIG-30',   'Tapete absorvente 60x60cm',                49.90,  28.00, 30, 5,  'UN', 'Higiene'),
(1, 'Coleira com Guia M',          'COL-GUI-M',    'Coleira + guia nylon tamanho M',           45.90,  22.00, 18, 3,  'UN', 'Acessórios'),
(1, 'Roupinha Canina P',           'ROU-CAN-P',    'Roupinha de moletom tamanho P',            55.90,  25.00, 12, 3,  'UN', 'Roupas'),
(1, 'Cama Pet Média',              'CAM-MED-01',   'Cama de pelúcia 50x50cm',                  89.90,  45.00, 10, 2,  'UN', 'Camas'),
(1, 'Comedouro Inox 500ml',        'COM-INO-500',  'Comedouro em inox antiaderente',           22.90,  10.00, 25, 5,  'UN', 'Acessórios'),
(1, 'Bebedouro Automático',        'BEB-AUT-01',   'Bebedouro com filtro 1,5L',                159.90, 85.00,  8, 2,  'UN', 'Acessórios'),
(1, 'Vermífugo Cães 4 comp.',      'VER-CAO-4',    'Vermífugo para cães até 10kg',             28.90,  14.00, 40, 10, 'UN', 'Medicamentos'),
(1, 'Antipulgas Spot-on Gato',     'ANT-GAT-01',   'Antipulgas pipeta para gatos',             42.90,  22.00, 30, 8,  'UN', 'Medicamentos'),
(1, 'Brinquedo Bolinha com Guizo', 'BRI-BOL-01',   'Bolinha com guizo para gatos',              9.90,   4.00, 50, 10, 'UN', 'Brinquedos'),
(1, 'Arranhador Gato Sisal',       'ARR-SIS-01',   'Arranhador com sisal 50cm',                69.90,  35.00, 12, 3,  'UN', 'Brinquedos'),
(1, 'Transportadora P',            'TRA-P-01',     'Caixa de transporte tamanho P',            149.90, 75.00,  7, 2,  'UN', 'Transporte');

-- ====================================
-- AGENDA (20 agendamentos - datas variadas)
-- ====================================
INSERT INTO agenda (company_id, pet_id, tutor_id, servico_id, profissional_id, data, hora, status, observacoes) VALUES
(1,  1,  1,  2,  2, DATE_ADD(CURDATE(), INTERVAL -20 DAY), '09:00', 'finalizado',     'Pet tranquilo'),
(1,  2,  1,  4,  1, DATE_ADD(CURDATE(), INTERVAL -18 DAY), '10:00', 'finalizado',     NULL),
(1,  3,  2,  5,  3, DATE_ADD(CURDATE(), INTERVAL -17 DAY), '14:00', 'finalizado',     'Gato nervoso, usar luvas'),
(1,  4,  3,  2,  2, DATE_ADD(CURDATE(), INTERVAL -15 DAY), '09:30', 'finalizado',     NULL),
(1,  5,  3,  4,  4, DATE_ADD(CURDATE(), INTERVAL -14 DAY), '11:00', 'finalizado',     NULL),
(1,  6,  4,  6,  1, DATE_ADD(CURDATE(), INTERVAL -13 DAY), '15:00', 'finalizado',     NULL),
(1,  7,  5,  7,  5, DATE_ADD(CURDATE(), INTERVAL -12 DAY), '09:00', 'finalizado',     NULL),
(1,  8,  5,  3,  1, DATE_ADD(CURDATE(), INTERVAL -10 DAY), '10:00', 'finalizado',     'Pelo longo, cuidado extra'),
(1,  9,  6,  1,  3, DATE_ADD(CURDATE(), INTERVAL -10 DAY), '14:00', 'finalizado',     NULL),
(1, 10,  7,  4,  4, DATE_ADD(CURDATE(), INTERVAL  -8 DAY), '09:00', 'finalizado',     NULL),
(1, 11,  8,  2,  2, DATE_ADD(CURDATE(), INTERVAL  -7 DAY), '10:30', 'finalizado',     NULL),
(1, 12,  9,  6,  5, DATE_ADD(CURDATE(), INTERVAL  -6 DAY), '11:00', 'finalizado',     NULL),
(1, 13, 10,  5,  1, DATE_ADD(CURDATE(), INTERVAL  -5 DAY), '09:00', 'finalizado',     'Gato, cuidado especial'),
(1, 14, 11,  2,  2, DATE_ADD(CURDATE(), INTERVAL  -5 DAY), '14:00', 'finalizado',     NULL),
(1, 15, 12,  4,  4, DATE_ADD(CURDATE(), INTERVAL  -3 DAY), '09:30', 'finalizado',     NULL),
(1, 16, 13,  3,  1, DATE_ADD(CURDATE(), INTERVAL  -3 DAY), '15:00', 'finalizado',     NULL),
(1, 17, 14,  2,  2, DATE_ADD(CURDATE(), INTERVAL  -2 DAY), '10:00', 'finalizado',     NULL),
(1,  1,  1,  2,  2, DATE_ADD(CURDATE(), INTERVAL   1 DAY), '09:00', 'confirmado',     NULL),
(1,  3,  2,  5,  3, DATE_ADD(CURDATE(), INTERVAL   1 DAY), '14:00', 'agendado',       NULL),
(1,  5,  3, 20,  1, DATE_ADD(CURDATE(), INTERVAL   2 DAY), '11:00', 'agendado',       'Pacote completo');

-- ====================================
-- VENDAS + ITENS (20 vendas)
-- ====================================
INSERT INTO vendas (company_id, tutor_id, pet_id, valor_total, desconto, valor_final, forma_pagamento, status, data) VALUES
(1,  1,  1,  189.90,  0.00,  189.90, 'pix',      'finalizada', DATE_ADD(NOW(), INTERVAL -20 DAY)),
(1,  2,  3,   45.90,  0.00,   45.90, 'dinheiro', 'finalizada', DATE_ADD(NOW(), INTERVAL -19 DAY)),
(1,  3,  4,  134.80, 10.00,  124.80, 'credito',  'finalizada', DATE_ADD(NOW(), INTERVAL -18 DAY)),
(1,  4,  5,   79.90,  0.00,   79.90, 'debito',   'finalizada', DATE_ADD(NOW(), INTERVAL -17 DAY)),
(1,  5,  7,   55.80,  0.00,   55.80, 'pix',      'finalizada', DATE_ADD(NOW(), INTERVAL -16 DAY)),
(1,  6,  9,  209.80,  9.80,  200.00, 'credito',  'finalizada', DATE_ADD(NOW(), INTERVAL -15 DAY)),
(1,  7, 10,   42.90,  0.00,   42.90, 'dinheiro', 'finalizada', DATE_ADD(NOW(), INTERVAL -14 DAY)),
(1,  8, 11,   89.90,  0.00,   89.90, 'pix',      'finalizada', DATE_ADD(NOW(), INTERVAL -13 DAY)),
(1,  9, 12,  159.90,  0.00,  159.90, 'debito',   'finalizada', DATE_ADD(NOW(), INTERVAL -12 DAY)),
(1, 10, 13,   29.80,  0.00,   29.80, 'dinheiro', 'finalizada', DATE_ADD(NOW(), INTERVAL -11 DAY)),
(1, 11, 14,  257.80,  7.80,  250.00, 'credito',  'finalizada', DATE_ADD(NOW(), INTERVAL -10 DAY)),
(1, 12, 15,   45.90,  0.00,   45.90, 'pix',      'finalizada', DATE_ADD(NOW(), INTERVAL  -9 DAY)),
(1, 13, 16,  119.80,  0.00,  119.80, 'debito',   'finalizada', DATE_ADD(NOW(), INTERVAL  -8 DAY)),
(1, 14, 17,   78.80,  0.00,   78.80, 'dinheiro', 'finalizada', DATE_ADD(NOW(), INTERVAL  -7 DAY)),
(1,  1,  2,   38.90,  0.00,   38.90, 'pix',      'finalizada', DATE_ADD(NOW(), INTERVAL  -6 DAY)),
(1,  2,  3,  189.90,  0.00,  189.90, 'credito',  'finalizada', DATE_ADD(NOW(), INTERVAL  -5 DAY)),
(1,  3,  4,   71.80,  1.80,   70.00, 'dinheiro', 'finalizada', DATE_ADD(NOW(), INTERVAL  -4 DAY)),
(1,  4,  6,  109.80,  0.00,  109.80, 'pix',      'finalizada', DATE_ADD(NOW(), INTERVAL  -3 DAY)),
(1,  5,  8,   55.90,  0.00,   55.90, 'debito',   'finalizada', DATE_ADD(NOW(), INTERVAL  -2 DAY)),
(1,  6, 11,  219.80, 19.80,  200.00, 'credito',  'finalizada', DATE_ADD(NOW(), INTERVAL  -1 DAY));

-- Itens das vendas
INSERT INTO venda_itens (venda_id, tipo_item, item_id, nome_item, quantidade, preco_unitario, preco_total) VALUES
(1,  'produto', 1,  'Ração Premium 15kg',            1, 189.90, 189.90),
(2,  'produto', 2,  'Shampoo Antipulgas',             1,  45.90,  45.90),
(3,  'produto', 1,  'Ração Premium 15kg',             1, 189.90, 189.90),
(3,  'produto', 5,  'Areia Sanitária 4kg',            1,  29.90,  29.90),
(4,  'produto', 3,  'Coleira Antipulgas',             1,  79.90,  79.90),
(5,  'produto', 4,  'Brinquedo Mordedor',             1,  25.90,  25.90),
(5,  'produto', 14, 'Brinquedo Bolinha com Guizo',    1,   9.90,   9.90),
(5,  'produto', 8,  'Roupinha Canina P',              1,  55.90,  55.90),
(6,  'produto', 9,  'Cama Pet Média',                 1,  89.90,  89.90),
(6,  'produto', 2,  'Shampoo Antipulgas',             1,  45.90,  45.90),
(6,  'produto', 11, 'Bebedouro Automático',           1, 159.90, 159.90),
(7,  'produto', 13, 'Antipulgas Spot-on Gato',        1,  42.90,  42.90),
(8,  'produto', 9,  'Cama Pet Média',                 1,  89.90,  89.90),
(9,  'produto', 11, 'Bebedouro Automático',           1, 159.90, 159.90),
(10, 'produto', 14, 'Brinquedo Bolinha com Guizo',    1,   9.90,   9.90),
(10, 'produto', 5,  'Areia Sanitária 4kg',            1,  29.90,  29.90),
(11, 'produto', 1,  'Ração Premium 15kg',             1, 189.90, 189.90),
(11, 'produto', 2,  'Shampoo Antipulgas',             1,  45.90,  45.90),
(11, 'produto', 6,  'Tapete Higiênico 30un',          1,  49.90,  49.90),
(12, 'produto', 13, 'Antipulgas Spot-on Gato',        1,  45.90,  45.90),
(13, 'produto', 7,  'Coleira com Guia M',             1,  45.90,  45.90),
(13, 'produto', 10, 'Comedouro Inox 500ml',           1,  22.90,  22.90),
(13, 'produto', 4,  'Brinquedo Mordedor',             1,  25.90,  25.90),
(14, 'produto', 6,  'Tapete Higiênico 30un',          1,  49.90,  49.90),
(14, 'produto', 14, 'Brinquedo Bolinha com Guizo',    1,   9.90,   9.90),
(14, 'produto', 12, 'Vermífugo Cães 4 comp.',         1,  28.90,  28.90),
(15, 'produto', 3,  'Shampoo Hidratante 500ml',       1,  38.90,  38.90),
(16, 'produto', 1,  'Ração Premium 15kg',             1, 189.90, 189.90),
(17, 'produto', 10, 'Comedouro Inox 500ml',           1,  22.90,  22.90),
(17, 'produto', 4,  'Brinquedo Mordedor',             1,  25.90,  25.90),
(17, 'produto', 8,  'Roupinha Canina P',              1,  55.90,  55.90),
(18, 'produto', 9,  'Cama Pet Média',                 1,  89.90,  89.90),
(18, 'produto', 7,  'Coleira com Guia M',             1,  45.90,  45.90),
(19, 'produto', 15, 'Arranhador Gato Sisal',          1,  69.90,  69.90),
(20, 'produto', 11, 'Bebedouro Automático',           1, 159.90, 159.90),
(20, 'produto', 2,  'Shampoo Antipulgas',             1,  45.90,  45.90),
(20, 'produto', 6,  'Tapete Higiênico 30un',          1,  49.90,  49.90);

-- ====================================
-- FINANCEIRO (20 lançamentos)
-- ====================================
INSERT INTO financeiro (company_id, tipo, categoria, descricao, valor, forma_pagamento, data_vencimento, data_pagamento, status) VALUES
-- Receitas de vendas
(1, 'receita',  'Vendas',          'Venda de produtos - semana 1',              634.50, 'pix',          DATE_ADD(CURDATE(), INTERVAL -20 DAY), DATE_ADD(CURDATE(), INTERVAL -20 DAY), 'pago'),
(1, 'receita',  'Serviços',        'Serviços de banho e tosa - semana 1',       480.00, 'dinheiro',     DATE_ADD(CURDATE(), INTERVAL -18 DAY), DATE_ADD(CURDATE(), INTERVAL -18 DAY), 'pago'),
(1, 'receita',  'Vendas',          'Venda de produtos - semana 2',              520.80, 'credito',      DATE_ADD(CURDATE(), INTERVAL -15 DAY), DATE_ADD(CURDATE(), INTERVAL -15 DAY), 'pago'),
(1, 'receita',  'Serviços',        'Serviços veterinários - semana 2',          360.00, 'pix',          DATE_ADD(CURDATE(), INTERVAL -13 DAY), DATE_ADD(CURDATE(), INTERVAL -13 DAY), 'pago'),
(1, 'receita',  'Vendas',          'Venda de produtos - semana 3',              748.90, 'debito',       DATE_ADD(CURDATE(), INTERVAL -10 DAY), DATE_ADD(CURDATE(), INTERVAL -10 DAY), 'pago'),
(1, 'receita',  'Serviços',        'Serviços de banho e tosa - semana 3',       540.00, 'pix',          DATE_ADD(CURDATE(), INTERVAL  -8 DAY), DATE_ADD(CURDATE(), INTERVAL  -8 DAY), 'pago'),
(1, 'receita',  'Vendas',          'Venda de produtos - semana 4',              615.70, 'credito',      DATE_ADD(CURDATE(), INTERVAL  -5 DAY), DATE_ADD(CURDATE(), INTERVAL  -5 DAY), 'pago'),
(1, 'receita',  'Serviços',        'Serviços de tosa artística - semana 4',     270.00, 'dinheiro',     DATE_ADD(CURDATE(), INTERVAL  -3 DAY), DATE_ADD(CURDATE(), INTERVAL  -3 DAY), 'pago'),
(1, 'receita',  'Outros',          'Venda de acessórios avulsos',               199.80, 'pix',          DATE_ADD(CURDATE(), INTERVAL  -1 DAY), DATE_ADD(CURDATE(), INTERVAL  -1 DAY), 'pago'),
(1, 'receita',  'Vendas',          'Vendas do dia - projeção',                  350.00, 'pix',          CURDATE(),                             NULL,                                  'pendente'),
-- Despesas
(1, 'despesa',  'Fornecedores',    'Compra de ração e petiscos - reposição',    1200.00, 'boleto',      DATE_ADD(CURDATE(), INTERVAL -22 DAY), DATE_ADD(CURDATE(), INTERVAL -20 DAY), 'pago'),
(1, 'despesa',  'Folha de Pagamento', 'Salários equipe - mês anterior',         8500.00, 'transferencia', DATE_ADD(CURDATE(), INTERVAL -10 DAY), DATE_ADD(CURDATE(), INTERVAL -10 DAY), 'pago'),
(1, 'despesa',  'Aluguel',         'Aluguel do ponto comercial',                2800.00, 'boleto',      DATE_ADD(CURDATE(), INTERVAL  -5 DAY), DATE_ADD(CURDATE(), INTERVAL  -5 DAY), 'pago'),
(1, 'despesa',  'Fornecedores',    'Compra de produtos de higiene - reposição',  680.00, 'pix',         DATE_ADD(CURDATE(), INTERVAL -12 DAY), DATE_ADD(CURDATE(), INTERVAL -12 DAY), 'pago'),
(1, 'despesa',  'Utilidades',      'Conta de energia elétrica',                  420.00, 'boleto',      DATE_ADD(CURDATE(), INTERVAL  -8 DAY), DATE_ADD(CURDATE(), INTERVAL  -8 DAY), 'pago'),
(1, 'despesa',  'Utilidades',      'Internet e telefone',                        180.00, 'debito',      DATE_ADD(CURDATE(), INTERVAL  -6 DAY), DATE_ADD(CURDATE(), INTERVAL  -6 DAY), 'pago'),
(1, 'despesa',  'Manutenção',      'Manutenção equipamentos banho e tosa',       350.00, 'dinheiro',    DATE_ADD(CURDATE(), INTERVAL  -4 DAY), DATE_ADD(CURDATE(), INTERVAL  -4 DAY), 'pago'),
(1, 'despesa',  'Marketing',       'Anúncios redes sociais',                     250.00, 'credito',     DATE_ADD(CURDATE(), INTERVAL  -2 DAY), DATE_ADD(CURDATE(), INTERVAL  -2 DAY), 'pago'),
(1, 'despesa',  'Fornecedores',    'Compra de acessórios e brinquedos',          540.00, 'boleto',      DATE_ADD(CURDATE(), INTERVAL   5 DAY), NULL,                                  'pendente'),
(1, 'despesa',  'Folha de Pagamento', 'Adiantamento salarial equipe',            2000.00, 'transferencia', DATE_ADD(CURDATE(), INTERVAL  10 DAY), NULL,                              'pendente');

SELECT 'Seed concluído!' as resultado;
