# 🐾 Pet Shop SaaS - Sistema de Gestão

Sistema completo para gestão de Pet Shops desenvolvido em PHP orientado a objetos com MySQL.

## 📋 Sobre o Sistema

Sistema SaaS multiempresa (multi-tenant) para gestão de Pet Shops com três tipos de planos:

- **Plano Banho e Tosa**: Agenda, Tutores, Pets, Serviços, Profissionais
- **Plano Loja/AgroPet**: Produtos, Vendas (PDV), Estoque, Financeiro, Relatórios
- **Plano Completo**: Todos os módulos

## 🚀 Tecnologias Utilizadas

- **Backend**: PHP 7.4+ (Orientado a Objetos)
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Arquitetura**: MVC Simplificado

## 📁 Estrutura do Projeto

```
petshop/
├── config/              # Configurações do sistema
│   └── config.php
├── database/            # Banco de dados
│   ├── connection.php
│   └── schema.sql
├── classes/             # Classes do sistema
│   ├── Tutor.class.php
│   ├── Pet.class.php
│   ├── Produto.class.php
│   ├── Servico.class.php
│   ├── Profissional.class.php
│   ├── Agenda.class.php
│   └── Venda.class.php
├── controllers/         # Controladores
│   ├── tutores.php
│   └── produtos.php
├── views/              # Interface do usuário
│   ├── template.php
│   └── tutores/
├── public/             # Arquivos públicos
│   ├── css/
│   ├── js/
│   ├── login.php
│   └── dashboard.php
├── helpers/            # Funções auxiliares
└── index.php
```

## ⚙️ Instalação

### 1. Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache ou Nginx
- Extensões PHP: PDO, pdo_mysql

### 2. Configuração do Banco de Dados

```bash
# Criar o banco de dados
mysql -u root -p

# Executar o script SQL
mysql -u root -p < database/schema.sql
```

Ou acesse o phpMyAdmin e importe o arquivo `database/schema.sql`

### 3. Configurar Conexão

Edite o arquivo `config/config.php` com suas credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'petshop_saas');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Configurar URL Base

No arquivo `config/config.php`, ajuste a URL:

```php
define('APP_URL', 'http://localhost/petshop');
```

### 5. Permissões (Linux/Mac)

```bash
chmod -R 755 /caminho/para/petshop
chmod -R 777 /caminho/para/petshop/public/uploads
```

## 🔐 Acesso ao Sistema

### Credenciais Padrão

- **E-mail**: admin@petshop.com
- **Senha**: admin123

**⚠️ IMPORTANTE**: Altere a senha padrão após o primeiro acesso!

## 📦 Módulos do Sistema

### Dashboard
- Faturamento do dia e do mês
- Agendamentos do dia
- Produtos com estoque baixo
- Estatísticas gerais

### Cadastros
- **Tutores**: Cadastro completo de clientes
- **Pets**: Informações dos animais vinculados aos tutores
- **Produtos**: Controle de produtos para venda
- **Serviços**: Cadastro de serviços oferecidos
- **Profissionais**: Gerenciamento de funcionários

### Agenda
- Agendamento de serviços
- Controle de status (agendado, confirmado, em atendimento, finalizado)
- Verificação de disponibilidade
- Histórico de atendimentos

### Vendas (PDV)
- Venda de produtos e serviços
- Baixa automática de estoque
- Registro financeiro automático
- Suporte a diversas formas de pagamento

### Estoque
- Controle de entrada e saída
- Movimentações detalhadas
- Alertas de estoque baixo
- Histórico completo

### Financeiro
- Controle de receitas e despesas
- Lançamentos automáticos de vendas
- Relatórios por período

## 🎯 Funcionalidades Principais

### Multi-tenant (Multiempresa)
- Isolamento total de dados entre empresas
- Cada empresa possui seu próprio conjunto de dados
- Campo `company_id` em todas as tabelas principais

### Sistema de Planos
- Controle de módulos disponíveis por plano
- Fácil expansão para novos planos
- Verificação automática de permissões

### Segurança
- Senhas criptografadas com password_hash()
- Proteção contra SQL Injection (PDO com prepared statements)
- Sanitização de dados de entrada
- Controle de sessão

## 🛠️ Desenvolvimento

### Adicionar Nova Classe

1. Criar arquivo em `classes/MinhaClasse.class.php`
2. Seguir o padrão das classes existentes
3. Implementar métodos CRUD básicos

```php
<?php
require_once __DIR__ . '/../database/connection.php';

class MinhaClasse {
    private $db;
    private $company_id;

    public function __construct($company_id = null) {
        $this->db = Database::getInstance();
        $this->company_id = $company_id ?? getCompanyId();
    }

    public function create() { }
    public function update() { }
    public function delete($id) { }
    public function getById($id) { }
    public function getAll($filtros = []) { }
}
```

### Adicionar Novo Controller

1. Criar arquivo em `controllers/meu_controller.php`
2. Seguir estrutura switch/case para actions
3. Incluir verificação de login e módulos

### Adicionar Nova View

1. Criar pasta em `views/minha_view/`
2. Criar arquivos: `list.php`, `form.php`, `view.php`
3. Usar template base: `views/template.php`

## 📊 Banco de Dados

### Principais Tabelas

- `companies` - Empresas cadastradas
- `users` - Usuários do sistema
- `tutors` - Tutores/Clientes
- `pets` - Animais de estimação
- `produtos` - Produtos para venda
- `servicos` - Serviços oferecidos
- `profissionais` - Funcionários
- `agenda` - Agendamentos
- `vendas` - Vendas realizadas
- `venda_itens` - Itens das vendas
- `estoque_movimentacoes` - Movimentações de estoque
- `financeiro` - Lançamentos financeiros

## 🔧 Manutenção

### Backup do Banco de Dados

```bash
mysqldump -u root -p petshop_saas > backup.sql
```

### Restaurar Backup

```bash
mysql -u root -p petshop_saas < backup.sql
```

## 📝 Notas Importantes

1. **Dados de Teste**: O sistema vem com dados de exemplo pré-cadastrados
2. **Senha do Admin**: A senha padrão está criptografada com `password_hash()`
3. **Multi-tenant**: Sempre use `company_id` nas queries
4. **Validações**: Implemente validações no frontend e backend

## 🐛 Solução de Problemas

### Erro de Conexão com Banco

- Verifique as credenciais em `config/config.php`
- Confirme que o MySQL está rodando
- Verifique se o banco de dados foi criado

### Erro de Permissão

```bash
chmod -R 755 /caminho/para/petshop
```

### Erro de Sessão

- Verifique se a extensão session do PHP está ativa
- Confirme permissões na pasta de sessões do PHP

## 📈 Próximos Passos / Melhorias Futuras

- [ ] API REST para integração
- [ ] App Mobile
- [ ] Sistema de notificações
- [ ] Relatórios em PDF
- [ ] Integração com WhatsApp
- [ ] Sistema de comissões detalhado
- [ ] Prontuário veterinário completo
- [ ] Agendamento online para clientes

## 👥 Suporte

Para dúvidas ou suporte, entre em contato através dos canais apropriados.

## 📄 Licença

Sistema desenvolvido para fins educacionais e comerciais.

---

**Desenvolvido com ❤️ para a comunidade Pet**
