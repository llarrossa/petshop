# 🗂️ ESTRUTURA COMPLETA DO PROJETO

```
petshop/
│
├── 📁 config/                          # Configurações do sistema
│   └── config.php                      # Configurações gerais (DB, URLs, constantes)
│
├── 📁 database/                        # Banco de dados
│   ├── connection.php                  # Classe de conexão PDO (Singleton)
│   └── schema.sql                      # Script de criação das tabelas
│
├── 📁 classes/                         # Classes do sistema (Model)
│   ├── Tutor.class.php                 # CRUD de tutores/clientes
│   ├── Pet.class.php                   # CRUD de pets/animais
│   ├── Produto.class.php               # CRUD de produtos
│   ├── Servico.class.php               # CRUD de serviços
│   ├── Profissional.class.php          # CRUD de profissionais
│   ├── Agenda.class.php                # CRUD de agendamentos
│   └── Venda.class.php                 # CRUD de vendas (PDV)
│
├── 📁 controllers/                     # Controladores (Controller)
│   ├── tutores.php                     # Processa ações de tutores
│   └── produtos.php                    # Processa ações de produtos
│
├── 📁 views/                           # Views/Interface (View)
│   ├── template.php                    # Template base do sistema
│   └── tutores/                        # Views de tutores
│       ├── list.php                    # Listagem de tutores
│       ├── form.php                    # Formulário (criar/editar)
│       └── view.php                    # Visualização detalhada
│
├── 📁 public/                          # Arquivos públicos acessíveis
│   ├── index.php                       # Redirecionamento inicial
│   ├── login.php                       # Tela de login
│   ├── authenticate.php                # Processamento de autenticação
│   ├── logout.php                      # Logout do sistema
│   ├── dashboard.php                   # Dashboard principal
│   │
│   ├── 📁 css/
│   │   └── style.css                   # Estilos CSS do sistema
│   │
│   ├── 📁 js/
│   │   └── script.js                   # JavaScript/jQuery
│   │
│   └── 📁 uploads/                     # Arquivos enviados (criada automaticamente)
│
├── 📁 helpers/                         # Funções auxiliares
│   └── functions.php                   # Funções utilitárias
│
├── 📁 logs/                            # Logs do sistema (criada automaticamente)
│
├── .htaccess                           # Configurações Apache
├── index.php                           # Ponto de entrada principal
├── README.md                           # Documentação completa
└── INSTALACAO.md                       # Guia de instalação passo a passo

```

## 📊 FLUXO DE DADOS DO SISTEMA

```
┌─────────────┐
│   Cliente   │
│  (Browser)  │
└──────┬──────┘
       │
       ↓
┌─────────────────────────────────────────┐
│        index.php / login.php            │
│     (Ponto de Entrada do Sistema)       │
└──────────────┬──────────────────────────┘
               │
               ↓
       ┌───────────────┐
       │  Autenticado? │
       └───────┬───────┘
               │
        ┌──────┴──────┐
        │             │
       NÃO           SIM
        │             │
        ↓             ↓
   ┌────────┐   ┌──────────┐
   │ Login  │   │Dashboard │
   └────────┘   └────┬─────┘
                     │
                     ↓
        ┌────────────────────────┐
        │   Menu de Navegação    │
        │  (Sidebar com Módulos) │
        └──────────┬─────────────┘
                   │
     ┌─────────────┼─────────────┬─────────────┐
     │             │             │             │
     ↓             ↓             ↓             ↓
┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐
│ Tutores │  │  Pets   │  │Produtos │  │ Agenda  │
└────┬────┘  └────┬────┘  └────┬────┘  └────┬────┘
     │            │            │            │
     ↓            ↓            ↓            ↓
┌──────────────────────────────────────────────┐
│         CONTROLLERS (PHP)                    │
│   Processam Actions: list, create,           │
│   edit, delete, view, etc.                   │
└─────────────────┬────────────────────────────┘
                  │
                  ↓
┌──────────────────────────────────────────────┐
│         CLASSES (Models)                     │
│   Lógica de Negócio e Acesso ao Banco       │
│   Métodos: create(), update(), delete(),     │
│   getAll(), getById()                        │
└─────────────────┬────────────────────────────┘
                  │
                  ↓
┌──────────────────────────────────────────────┐
│       DATABASE (MySQL)                       │
│   Tabelas: companies, users, tutors, pets,  │
│   produtos, servicos, agenda, vendas, etc.   │
└──────────────────────────────────────────────┘
```

## 🔄 ARQUITETURA MULTI-TENANT

```
┌─────────────────────────────────────────────┐
│           CAMADA DE APLICAÇÃO               │
│  (Cada empresa acessa o mesmo código)       │
└─────────────────┬───────────────────────────┘
                  │
       ┌──────────┴──────────┐
       │                     │
       ↓                     ↓
┌─────────────┐      ┌─────────────┐
│  Empresa 1  │      │  Empresa 2  │
│ (Pet Shop A)│      │ (Pet Shop B)│
└──────┬──────┘      └──────┬──────┘
       │                    │
       │ company_id = 1     │ company_id = 2
       │                    │
       ↓                    ↓
┌─────────────────────────────────────────────┐
│           BANCO DE DADOS MYSQL              │
│                                             │
│  ┌──────────────┐  ┌──────────────┐        │
│  │ Tutor A1     │  │ Tutor B1     │        │
│  │ company_id=1 │  │ company_id=2 │        │
│  └──────────────┘  └──────────────┘        │
│                                             │
│  ┌──────────────┐  ┌──────────────┐        │
│  │ Pet A1       │  │ Pet B1       │        │
│  │ company_id=1 │  │ company_id=2 │        │
│  └──────────────┘  └──────────────┘        │
│                                             │
│  *** Isolamento total entre empresas ***   │
└─────────────────────────────────────────────┘
```

## 🎯 PLANOS E MÓDULOS

```
┌─────────────────────────────────────────────┐
│           SISTEMA DE PLANOS                 │
└─────────────────────────────────────────────┘

┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│ Banho e Tosa    │  │  Loja/AgroPet   │  │    Completo     │
├─────────────────┤  ├─────────────────┤  ├─────────────────┤
│ ✓ Dashboard     │  │ ✓ Dashboard     │  │ ✓ Dashboard     │
│ ✓ Tutores       │  │ ✓ Produtos      │  │ ✓ Tutores       │
│ ✓ Pets          │  │ ✓ Vendas (PDV)  │  │ ✓ Pets          │
│ ✓ Serviços      │  │ ✓ Estoque       │  │ ✓ Serviços      │
│ ✓ Profissionais │  │ ✓ Financeiro    │  │ ✓ Profissionais │
│ ✓ Agenda        │  │ ✓ Relatórios    │  │ ✓ Produtos      │
│                 │  │                 │  │ ✓ Agenda        │
│                 │  │                 │  │ ✓ Vendas (PDV)  │
│                 │  │                 │  │ ✓ Estoque       │
│                 │  │                 │  │ ✓ Financeiro    │
│                 │  │                 │  │ ✓ Prontuário    │
│                 │  │                 │  │ ✓ Relatórios    │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

## 🔐 FLUXO DE AUTENTICAÇÃO

```
1. Usuário acessa: http://localhost/petshop
   ↓
2. index.php verifica se está logado
   ↓
3. NÃO está logado → Redireciona para login.php
   ↓
4. Usuário preenche e-mail e senha
   ↓
5. Form envia para authenticate.php
   ↓
6. authenticate.php:
   - Busca usuário no banco
   - Verifica senha com password_verify()
   - Carrega dados da empresa (company_id, plano)
   ↓
7. Login OK → Cria sessão:
   $_SESSION['user_id']
   $_SESSION['company_id']
   $_SESSION['plano']
   ↓
8. Redireciona para dashboard.php
   ↓
9. Dashboard carrega dados filtrados por company_id
```

## 📦 PRINCIPAIS TECNOLOGIAS

```
Backend:
├── PHP 7.4+
├── PDO (MySQL)
└── Orientação a Objetos

Frontend:
├── HTML5
├── CSS3 (Flexbox/Grid)
└── JavaScript (jQuery)

Banco de Dados:
└── MySQL 5.7+

Padrões:
├── MVC Simplificado
├── Singleton (Database)
├── Multi-tenant
└── CRUD Completo
```

## 💡 CONCEITOS IMPORTANTES

### Multi-tenant (Multiempresa)
- Todas as tabelas possuem `company_id`
- Queries sempre filtram por `company_id`
- Isolamento total de dados entre empresas

### Segurança
- Senhas criptografadas (password_hash)
- Prepared Statements (PDO)
- Sanitização de dados (htmlspecialchars)
- Controle de sessão

### Modularidade
- Sistema de planos controla módulos disponíveis
- Função moduloDisponivel() verifica acesso
- Fácil expansão de funcionalidades

---

**Sistema completo e pronto para uso!** 🚀
