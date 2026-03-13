# Cenários de Teste — Pawfy Pet Shop SaaS

> **Sistema:** Pawfy
> **Versão:** 1.0.0
> **Data:** 2026-03-11
> **Escopo:** Varredura completa de ponta a ponta

---

## Índice

1. [Autenticação e Cadastro](#1-autenticação-e-cadastro)
2. [Dashboard](#2-dashboard)
3. [Clientes (Tutores)](#3-clientes-tutores)
4. [Pets](#4-pets)
5. [Serviços](#5-serviços)
6. [Profissionais](#6-profissionais)
7. [Agenda](#7-agenda)
8. [Produtos e Estoque](#8-produtos-e-estoque)
9. [Vendas (PDV)](#9-vendas-pdv)
10. [Financeiro](#10-financeiro)
11. [Relatórios](#11-relatórios)
12. [Planos e Assinatura](#12-planos-e-assinatura)
13. [Multi-tenancy e Isolamento de Dados](#13-multi-tenancy-e-isolamento-de-dados)
14. [Controle de Acesso por Plano](#14-controle-de-acesso-por-plano)
15. [Fluxos Integrados de Ponta a Ponta](#15-fluxos-integrados-de-ponta-a-ponta)

---

## Convenções

| Campo | Descrição |
|-------|-----------|
| **ID** | Identificador único do cenário |
| **Pré-condição** | Estado necessário antes de executar |
| **Passos** | Ações a realizar |
| **Resultado Esperado** | O que deve acontecer |
| **Status** | `[ ]` pendente / `[x]` passou / `[!]` falhou |

---

## 1. Autenticação e Cadastro

### CT-AUTH-01 — Cadastro de nova empresa (caminho feliz)
- **Pré-condição:** Usuário não logado; email `novo@petshop.com` inexistente no banco
- **Passos:**
  1. Acessar `public/register.php`
  2. Preencher **Nome do Pet Shop:** `Pet Shop Teste`
  3. Preencher **CNPJ:** `12.345.678/0001-90`
  4. Preencher **Nome completo:** `João Teste`
  5. Preencher **E-mail:** `novo@petshop.com`
  6. Preencher **Senha:** `senha123`
  7. Preencher **Confirmar senha:** `senha123`
  8. Clicar em "Começar teste grátis"
- **Resultado Esperado:**
  - Empresa inserida em `companies` com `subscription_status = 'trialing'` e `trial_ends_at = agora + 7 dias`
  - Usuário inserido em `users` com `perfil = 'admin'` e senha em hash bcrypt
  - Auto-login: sessão com `user_id`, `company_id`, `plano = 'completo'`, `subscription_status = 'trialing'`
  - Redirecionamento para `?page=dashboard&trial_novo=1`
- **Status:** `[ ]`

---

### CT-AUTH-02 — Cadastro com email já existente
- **Pré-condição:** Email `existente@petshop.com` já cadastrado
- **Passos:**
  1. Acessar `public/register.php`
  2. Preencher todos os campos corretamente usando `existente@petshop.com`
  3. Submeter
- **Resultado Esperado:**
  - Formulário reexibido com mensagem `"Já existe uma conta cadastrada com este e-mail."`
  - Nenhuma empresa ou usuário inserido
  - Campos do formulário repopulados (exceto senhas)
- **Status:** `[ ]`

---

### CT-AUTH-03 — Cadastro com campos obrigatórios em branco
- **Pré-condição:** Nenhuma
- **Passos:**
  1. Acessar `public/register.php`
  2. Submeter formulário sem preencher nenhum campo
- **Resultado Esperado:**
  - Mensagens de erro para cada campo obrigatório:
    - `"O nome do pet shop é obrigatório."`
    - `"O nome do responsável é obrigatório."`
    - `"Informe um e-mail válido."`
    - `"A senha deve ter no mínimo 8 caracteres."`
  - Nenhuma inserção no banco
- **Status:** `[ ]`

---

### CT-AUTH-04 — Cadastro com senha menor que 8 caracteres
- **Pré-condição:** Nenhuma
- **Passos:**
  1. Preencher todos os campos válidos
  2. Colocar senha `abc123` (6 caracteres)
- **Resultado Esperado:** Erro `"A senha deve ter no mínimo 8 caracteres."`
- **Status:** `[ ]`

---

### CT-AUTH-05 — Cadastro com senhas diferentes
- **Pré-condição:** Nenhuma
- **Passos:**
  1. Preencher todos os campos válidos
  2. Senha: `senha123`, Confirmar: `senha456`
- **Resultado Esperado:** Erro `"As senhas não coincidem."`
- **Status:** `[ ]`

---

### CT-AUTH-06 — Cadastro com CNPJ opcional em branco
- **Pré-condição:** Email único
- **Passos:**
  1. Preencher todos os campos obrigatórios; deixar CNPJ em branco
  2. Submeter
- **Resultado Esperado:**
  - Cadastro realizado com sucesso
  - Coluna `cnpj` salva como `NULL` no banco
- **Status:** `[ ]`

---

### CT-AUTH-07 — Login com credenciais corretas
- **Pré-condição:** Empresa e usuário existentes com assinatura ativa
- **Passos:**
  1. Acessar `public/login.php`
  2. Preencher email e senha corretos
  3. Clicar em "Entrar"
- **Resultado Esperado:**
  - Sessão criada com todos os campos: `user_id`, `user_name`, `user_email`, `company_id`, `company_name`, `plano`, `perfil`, `subscription_status`, `trial_ends_at`
  - Redirecionamento para `?page=dashboard`
- **Status:** `[ ]`

---

### CT-AUTH-08 — Login com senha incorreta
- **Pré-condição:** Usuário existente
- **Passos:**
  1. Acessar `public/login.php`
  2. Email correto, senha errada
  3. Submeter
- **Resultado Esperado:**
  - Nenhuma sessão criada
  - Mensagem de erro exibida na tela de login
  - Permanece na tela de login
- **Status:** `[ ]`

---

### CT-AUTH-09 — Login com email inexistente
- **Pré-condição:** Nenhuma
- **Passos:**
  1. Email `naoexiste@x.com`, qualquer senha
- **Resultado Esperado:** Mensagem de erro; sem sessão criada
- **Status:** `[ ]`

---

### CT-AUTH-10 — Logout
- **Pré-condição:** Usuário logado
- **Passos:**
  1. Acessar `public/logout.php`
- **Resultado Esperado:**
  - Sessão destruída
  - Redirecionamento para `public/login.php`
  - Tentativa de acessar rota protegida resulta em redirecionamento para login
- **Status:** `[ ]`

---

### CT-AUTH-11 — Acesso a rota protegida sem sessão
- **Pré-condição:** Usuário não logado
- **Passos:**
  1. Tentar acessar diretamente `index.php?page=clientes`
- **Resultado Esperado:** Redirecionamento para `public/login.php`
- **Status:** `[ ]`

---

### CT-AUTH-12 — Usuário já logado acessa /register.php
- **Pré-condição:** Usuário com sessão ativa
- **Passos:**
  1. Acessar `public/register.php` estando logado
- **Resultado Esperado:** Redirecionamento para `?page=dashboard`
- **Status:** `[ ]`

---

### CT-AUTH-13 — Login com trial ainda ativo
- **Pré-condição:** Empresa com `subscription_status = 'trialing'` e `trial_ends_at` no futuro
- **Passos:**
  1. Login com credenciais da empresa em trial
- **Resultado Esperado:**
  - Login bem-sucedido
  - Redirecionamento para dashboard
  - `$_SESSION['subscription_status'] = 'trialing'`
- **Status:** `[ ]`

---

### CT-AUTH-14 — Login com trial expirado
- **Pré-condição:** Empresa com `trial_ends_at` no passado e sem assinatura ativa
- **Passos:**
  1. Login com credenciais da empresa
- **Resultado Esperado:**
  - Autenticação ocorre
  - `subscription_status` atualizado para `'incomplete'`
  - Redirecionamento para `public/planos.php` (não para dashboard)
- **Status:** `[ ]`

---

## 2. Dashboard

### CT-DASH-01 — Exibição dos KPIs do dia
- **Pré-condição:** Empresa com vendas, agendamentos e produtos cadastrados; usuário logado
- **Passos:**
  1. Acessar `?page=dashboard`
- **Resultado Esperado:**
  - **Card "Faturamento Hoje":** Soma correta das vendas `status = 'finalizada'` da data atual
  - **Card "Faturamento do Mês":** Soma das vendas finalizadas no mês corrente
  - **Card "Agendamentos Hoje":** Contagem dos agendamentos da data atual
  - **Card "Estoque Baixo":** Contagem de produtos com `estoque_atual < estoque_minimo`
- **Status:** `[ ]`

---

### CT-DASH-02 — Próximos agendamentos (módulo agenda ativo)
- **Pré-condição:** Plano com módulo `agenda`; pelo menos 1 agendamento futuro
- **Passos:**
  1. Acessar `?page=dashboard`
- **Resultado Esperado:**
  - Seção "Próximos Agendamentos" visível com até 5 registros
  - Informações: pet, tutor, serviço, profissional, data/hora, status
- **Status:** `[ ]`

---

### CT-DASH-03 — Produtos com estoque baixo (módulo produtos ativo)
- **Pré-condição:** Plano com módulo `produtos`; produto com `estoque_atual < estoque_minimo`
- **Passos:**
  1. Acessar `?page=dashboard`
- **Resultado Esperado:**
  - Seção "Estoque Baixo" exibe os produtos com alerta
- **Status:** `[ ]`

---

### CT-DASH-04 — Últimas vendas (módulo vendas ativo)
- **Pré-condição:** Plano com módulo `vendas`; pelo menos 1 venda cadastrada
- **Passos:**
  1. Acessar `?page=dashboard`
- **Resultado Esperado:**
  - Seção "Últimas Vendas" exibida com dados corretos
- **Status:** `[ ]`

---

### CT-DASH-05 — Dashboard sem dados (empresa nova)
- **Pré-condição:** Empresa recém-criada, sem nenhum cadastro
- **Passos:**
  1. Acessar `?page=dashboard`
- **Resultado Esperado:**
  - Todos os KPIs exibem `R$ 0,00` ou `0`
  - Nenhum erro PHP
  - Seções de listas exibem estado vazio (sem crash)
- **Status:** `[ ]`

---

### CT-DASH-06 — Alerta de trial novo na primeira entrada
- **Pré-condição:** Usuário recém-cadastrado com parâmetro `trial_novo=1`
- **Passos:**
  1. Após cadastro, acessar `?page=dashboard&trial_novo=1`
- **Resultado Esperado:**
  - Mensagem de boas-vindas ao trial exibida na tela
- **Status:** `[ ]`

---

## 3. Clientes (Tutores)

### CT-CLI-01 — Listar clientes
- **Pré-condição:** Pelo menos 3 clientes cadastrados
- **Passos:**
  1. Acessar `?page=clientes&action=list`
- **Resultado Esperado:**
  - Lista exibe apenas clientes da empresa logada (company_id correto)
  - Colunas: nome, CPF, telefone, cidade, status
  - Paginação ativa (20 registros por página)
- **Status:** `[x]`

---

### CT-CLI-02 — Filtrar clientes por nome
- **Pré-condição:** Clientes com nomes variados cadastrados
- **Passos:**
  1. Acessar `?page=clientes&action=list&busca=João`
- **Resultado Esperado:**
  - Apenas clientes cujo nome contenha "João" são exibidos
  - Clientes sem "João" no nome não aparecem
- **Status:** `[x]`

---

### CT-CLI-03 — Filtrar clientes por status
- **Pré-condição:** Clientes ativos e inativos cadastrados
- **Passos:**
  1. Filtrar por `status=ativo`
  2. Filtrar por `status=inativo`
- **Resultado Esperado:**
  - Cada filtro retorna apenas clientes do respectivo status
- **Status:** `[x]`

---

### CT-CLI-04 — Ordenação de clientes
- **Pré-condição:** Pelo menos 3 clientes cadastrados
- **Passos:**
  1. Clicar no cabeçalho "Nome" para ordenar A→Z
  2. Clicar novamente para ordenar Z→A
- **Resultado Esperado:**
  - Lista reordena corretamente em ambas as direções
- **Status:** `[x]`

---

### CT-CLI-05 — Criar cliente com todos os campos
- **Pré-condição:** Usuário logado
- **Passos:**
  1. Acessar `?page=clientes&action=create`
  2. Preencher: Nome `Maria Silva`, CPF `123.456.789-09`, Telefone `(11) 9999-8888`, WhatsApp `(11) 9999-7777`, Email `maria@email.com`, Endereço `Rua das Flores, 100`, Cidade `São Paulo`, Estado `SP`, CEP `01310-100`, Observações `Cliente VIP`, Status `ativo`
  3. Salvar
- **Resultado Esperado:**
  - Registro inserido com `company_id` correto
  - Redirecionamento para lista com mensagem de sucesso
  - Cliente aparece na listagem
- **Status:** `[x]`

---

### CT-CLI-06 — Criar cliente com campos mínimos
- **Pré-condição:** Usuário logado
- **Passos:**
  1. Preencher apenas o Nome, deixar demais campos em branco
  2. Salvar
- **Resultado Esperado:**
  - Cadastro salvo sem erros
  - Campos opcionais salvos como `NULL` ou string vazia conforme o banco
- **Status:** `[x]`

---

### CT-CLI-07 — Editar cliente
- **Pré-condição:** Cliente existente
- **Passos:**
  1. Acessar `?page=clientes&action=edit&id={ID}`
  2. Alterar telefone para `(11) 1111-2222`
  3. Salvar
- **Resultado Esperado:**
  - Dado atualizado no banco
  - Mensagem de sucesso exibida
  - Lista reflete a alteração
- **Status:** `[x]`

---

### CT-CLI-08 — Visualizar detalhes do cliente
- **Pré-condição:** Cliente com pets, vendas e agendamentos associados
- **Passos:**
  1. Acessar `?page=clientes&action=view&id={ID}`
- **Resultado Esperado:**
  - Dados do cliente exibidos
  - Lista de pets do cliente
  - Histórico de vendas
  - Histórico de agendamentos
- **Status:** `[x]`

---

### CT-CLI-09 — Deletar cliente sem vínculos
- **Pré-condição:** Cliente sem pets, vendas ou agendamentos associados
- **Passos:**
  1. Acessar `?page=clientes&action=delete&id={ID}`
- **Resultado Esperado:**
  - Cliente removido do banco
  - Mensagem de sucesso
  - Não aparece mais na listagem
- **Status:** `[x]`

---

### CT-CLI-10 — Deletar cliente com vínculos
- **Pré-condição:** Cliente com pets ou vendas associados
- **Passos:**
  1. Tentar deletar via `?page=clientes&action=delete&id={ID}`
- **Resultado Esperado:**
  - Operação bloqueada por integridade referencial (FK) OU mensagem de erro amigável exibida
  - Cliente não removido
- **Status:** `[x]`

---

### CT-CLI-11 — Busca AJAX de clientes
- **Pré-condição:** Pelo menos 1 cliente cadastrado
- **Passos:**
  1. Fazer GET para `?page=clientes&action=buscar&termo=Maria`
- **Resultado Esperado:**
  - Resposta JSON com array de clientes que correspondem ao termo
  - Retorno restrito à empresa logada (company_id)
- **Status:** `[ ]`

---

### CT-CLI-12 — Editar cliente de outra empresa (segurança)
- **Pré-condição:** Duas empresas A e B; cliente da empresa B
- **Passos:**
  1. Logado como empresa A, tentar acessar `?page=clientes&action=edit&id={ID_EMPRESA_B}`
- **Resultado Esperado:**
  - Formulário vazio ou redirecionamento com erro `"Cliente não encontrado."`
  - Dado da empresa B não exposto
- **Status:** `[x]`

---

## 4. Pets

### CT-PET-01 — Criar pet vinculado a cliente
- **Pré-condição:** Cliente ativo cadastrado
- **Passos:**
  1. Acessar `?page=pets&action=create`
  2. Preencher: Nome `Rex`, Tutor (selecionar cliente), Espécie `Cão`, Raça `Labrador`, Sexo `Macho`, Nascimento `2020-05-15`, Peso `28.5`, Cor `Amarelo`, Porte `Grande`
  3. Salvar
- **Resultado Esperado:**
  - Pet inserido com `tutor_id` e `company_id` corretos
  - Aparece na lista e no perfil do cliente
- **Status:** `[x]`

---

### CT-PET-02 — Criar pet para espécies diferentes
- **Pré-condição:** Cliente ativo
- **Passos:**
  1. Criar pet com espécie `Gato`
  2. Criar pet com espécie `Pássaro`
  3. Criar pet com espécie `Outro`
- **Resultado Esperado:**
  - Todos criados sem erro; espécie salva corretamente
- **Status:** `[x]`

---

### CT-PET-03 — Listar e filtrar pets por tutor
- **Pré-condição:** Múltiplos pets de tutores diferentes
- **Passos:**
  1. Filtrar por tutor específico via `?page=pets&action=list&tutor_id={ID}`
- **Resultado Esperado:**
  - Apenas pets do tutor filtrado exibidos
- **Status:** `[x]`

---

### CT-PET-04 — Listar e filtrar pets por espécie
- **Pré-condição:** Pets de espécies diferentes
- **Passos:**
  1. Filtrar por `especie=Cão`
- **Resultado Esperado:**
  - Apenas cães listados
- **Status:** `[x]`

---

### CT-PET-05 — Visualizar prontuário do pet
- **Pré-condição:** Pet com agendamentos e atendimentos realizados
- **Passos:**
  1. Acessar `?page=pets&action=view&id={ID}`
- **Resultado Esperado:**
  - Dados do pet exibidos
  - Histórico de atendimentos/prontuário exibido
- **Status:** `[ ]`

---

### CT-PET-06 — Editar pet
- **Pré-condição:** Pet existente
- **Passos:**
  1. Editar peso para `30.0` e observações para `Alérgico a pulgas`
  2. Salvar
- **Resultado Esperado:**
  - Alterações persistidas corretamente
- **Status:** `[x]`

---

### CT-PET-07 — Deletar pet sem histórico
- **Pré-condição:** Pet sem agendamentos ou vendas associadas
- **Passos:**
  1. Acessar `?page=pets&action=delete&id={ID}`
- **Resultado Esperado:**
  - Pet removido; mensagem de sucesso
- **Status:** `[x]`

---

### CT-PET-08 — Busca AJAX de pets por tutor
- **Pré-condição:** Tutor com pets cadastrados
- **Passos:**
  1. GET `?page=pets&action=buscar&tutor_id={ID}`
- **Resultado Esperado:**
  - JSON com pets do tutor especificado
- **Status:** `[ ]`

---

### CT-PET-09 — Busca AJAX de pets por nome/raça
- **Pré-condição:** Pets cadastrados
- **Passos:**
  1. GET `?page=pets&action=buscar&termo=Labrador`
- **Resultado Esperado:**
  - JSON com pets que correspondem ao termo
- **Status:** `[ ]`

---

## 5. Serviços

### CT-SVC-01 — Criar serviço
- **Pré-condição:** Módulo `banho_tosa` ou `completo` ativo
- **Passos:**
  1. Acessar `?page=servicos&action=create`
  2. Preencher: Nome `Banho e Tosa Completo`, Descrição `Inclui banho, secagem e corte`, Preço `80.00`, Duração `90`, Categoria `Banho & Tosa`, Status `ativo`
  3. Salvar
- **Resultado Esperado:**
  - Serviço inserido com `company_id` correto
  - Aparece na lista de serviços
- **Status:** `[x]`

---

### CT-SVC-02 — Listar serviços com filtro por categoria
- **Pré-condição:** Serviços de categorias diferentes
- **Passos:**
  1. Filtrar por categoria `Banho & Tosa`
- **Resultado Esperado:**
  - Apenas serviços da categoria filtrada exibidos
- **Status:** `[ ]`

---

### CT-SVC-03 — Filtrar serviços por status
- **Pré-condição:** Serviços ativos e inativos
- **Passos:**
  1. Filtrar por `status=inativo`
- **Resultado Esperado:**
  - Apenas serviços inativos listados
- **Status:** `[x]`

---

### CT-SVC-04 — Editar serviço
- **Pré-condição:** Serviço existente
- **Passos:**
  1. Alterar preço de `80.00` para `95.00`
  2. Salvar
- **Resultado Esperado:**
  - Preço atualizado no banco
  - Novos agendamentos e vendas usam o novo preço como sugestão
- **Status:** `[x]`

---

### CT-SVC-05 — Deletar serviço
- **Pré-condição:** Serviço sem vínculos ativos
- **Passos:**
  1. `?page=servicos&action=delete&id={ID}`
- **Resultado Esperado:**
  - Serviço removido ou inativado; mensagem de sucesso
- **Status:** `[x]`

---

### CT-SVC-06 — Busca AJAX de serviços
- **Pré-condição:** Serviços cadastrados
- **Passos:**
  1. GET `?page=servicos&action=buscar&termo=Banho`
- **Resultado Esperado:**
  - JSON com serviços que correspondem ao termo
- **Status:** `[ ]`

---

## 6. Profissionais

### CT-PROF-01 — Criar profissional com comissão percentual
- **Pré-condição:** Módulo `banho_tosa` ou `completo` ativo
- **Passos:**
  1. Acessar `?page=profissionais&action=create`
  2. Preencher: Nome `Carlos Tosador`, Função `Tosador`, Telefone `(11) 8888-7777`, Email `carlos@petshop.com`, Comissão `15`, Tipo de comissão `percentual`, Status `ativo`
  3. Salvar
- **Resultado Esperado:**
  - Profissional inserido com `company_id` correto
  - Aparece na listagem e está disponível na agenda/vendas
- **Status:** `[x]`

---

### CT-PROF-02 — Criar profissional com comissão fixa
- **Pré-condição:** Módulo disponível
- **Passos:**
  1. Cadastrar com Comissão `50.00` e Tipo `fixo`
- **Resultado Esperado:**
  - Tipo de comissão salvo como `fixo`
- **Status:** `[ ]`

---

### CT-PROF-03 — Editar profissional
- **Pré-condição:** Profissional existente
- **Passos:**
  1. Alterar comissão de `15%` para `20%`
- **Resultado Esperado:**
  - Comissão atualizada no banco
- **Status:** `[x]`

---

### CT-PROF-04 — Filtrar profissionais por status
- **Pré-condição:** Profissionais ativos e inativos
- **Passos:**
  1. Filtrar por `status=ativo`
- **Resultado Esperado:**
  - Apenas profissionais ativos listados
- **Status:** `[x]`

---

### CT-PROF-05 — Deletar profissional
- **Pré-condição:** Profissional sem agendamentos futuros
- **Passos:**
  1. `?page=profissionais&action=delete&id={ID}`
- **Resultado Esperado:**
  - Profissional removido; mensagem de sucesso
- **Status:** `[x]`

---

## 7. Agenda

### CT-AGE-01 — Criar agendamento completo
- **Pré-condição:** Pet, tutor, serviço e profissional ativos cadastrados; módulo `agenda` ativo
- **Passos:**
  1. Acessar `?page=agenda&action=create`
  2. Selecionar Pet, Tutor, Serviço `Banho e Tosa`, Profissional `Carlos`, Data `2026-03-20`, Hora `10:00`, Observações `Levar ração especial`
  3. Salvar
- **Resultado Esperado:**
  - Agendamento inserido com `status = 'agendado'`
  - Aparece na listagem da data selecionada
  - Exibido no dashboard em "Próximos Agendamentos"
- **Status:** `[x]`

---

### CT-AGE-02 — Criar agendamento sem profissional (campo opcional)
- **Pré-condição:** Pet e serviço cadastrados
- **Passos:**
  1. Criar agendamento sem selecionar profissional
- **Resultado Esperado:**
  - Agendamento criado com `profissional_id = NULL`
- **Status:** `[x]`

---

### CT-AGE-03 — Conflito de horário para o mesmo profissional
- **Pré-condição:** Profissional com agendamento às `10:00` em `2026-03-20`
- **Passos:**
  1. Tentar criar novo agendamento no mesmo profissional, data e hora
- **Resultado Esperado:**
  - Erro `"Verifique a disponibilidade do horário."` exibido
  - Nenhum novo agendamento inserido
- **Status:** `[x]`

---

### CT-AGE-04 — Listar agendamentos com filtro por data
- **Pré-condição:** Agendamentos em datas diferentes
- **Passos:**
  1. Filtrar por `data=2026-03-20`
- **Resultado Esperado:**
  - Apenas agendamentos da data `2026-03-20` exibidos
- **Status:** `[x]`

---

### CT-AGE-05 — Filtrar agendamentos por status
- **Pré-condição:** Agendamentos com status variados
- **Passos:**
  1. Filtrar por `status=finalizado`
- **Resultado Esperado:**
  - Apenas agendamentos finalizados exibidos
- **Status:** `[x]`

---

### CT-AGE-06 — Filtrar agendamentos por profissional
- **Pré-condição:** Agendamentos para profissionais diferentes
- **Passos:**
  1. Filtrar por `profissional_id={ID}`
- **Resultado Esperado:**
  - Apenas agendamentos do profissional selecionado listados
- **Status:** `[x]`

---

### CT-AGE-07 — Alterar status: agendado → confirmado
- **Pré-condição:** Agendamento com `status = 'agendado'`
- **Passos:**
  1. `?page=agenda&action=status&id={ID}&novo_status=confirmado`
- **Resultado Esperado:**
  - Status atualizado para `confirmado`; mensagem de sucesso
- **Status:** `[x]`

---

### CT-AGE-08 — Progressão completa de status
- **Pré-condição:** Agendamento recém-criado
- **Passos:**
  1. `agendado` → `confirmado`
  2. `confirmado` → `em_atendimento`
  3. `em_atendimento` → `finalizado`
- **Resultado Esperado:**
  - Cada transição persiste corretamente no banco
- **Status:** `[x]`

---

### CT-AGE-09 — Marcar como "faltou" e "cancelado"
- **Pré-condição:** Agendamentos ativos
- **Passos:**
  1. Alterar status para `faltou`
  2. Alterar status para `cancelado`
- **Resultado Esperado:**
  - Ambos os status aceitos e persistidos
- **Status:** `[x]`

---

### CT-AGE-10 — Enviar status inválido (segurança)
- **Pré-condição:** Agendamento existente
- **Passos:**
  1. `?page=agenda&action=status&id={ID}&novo_status=invalido`
- **Resultado Esperado:**
  - Status não alterado
  - Mensagem de erro exibida
- **Status:** `[ ]`

---

### CT-AGE-11 — Editar agendamento
- **Pré-condição:** Agendamento com `status = 'agendado'`
- **Passos:**
  1. `?page=agenda&action=edit&id={ID}`
  2. Alterar hora de `10:00` para `14:00`
  3. Salvar
- **Resultado Esperado:**
  - Hora atualizada no banco; mensagem de sucesso
- **Status:** `[ ]`

---

### CT-AGE-12 — Deletar agendamento
- **Pré-condição:** Agendamento existente
- **Passos:**
  1. `?page=agenda&action=delete&id={ID}`
- **Resultado Esperado:**
  - Agendamento removido; não aparece mais na lista
- **Status:** `[ ]`

---

## 8. Produtos e Estoque

### CT-PROD-01 — Criar produto com estoque inicial
- **Pré-condição:** Módulo `loja` ou `completo` ativo
- **Passos:**
  1. Acessar `?page=produtos&action=create`
  2. Preencher: Nome `Ração Premium 15kg`, SKU `RAC001`, Categoria `Alimentação`, Preço Venda `120.00`, Preço Custo `80.00`, Estoque Inicial `50`, Estoque Mínimo `10`, Unidade `un`
  3. Salvar
- **Resultado Esperado:**
  - Produto inserido com `estoque_atual = 50`
  - Movimentação de entrada registrada em `estoque_movimentacoes`
  - Aparece na listagem
- **Status:** `[x]`

---

### CT-PROD-02 — Filtrar produtos por categoria
- **Pré-condição:** Produtos de categorias diferentes
- **Passos:**
  1. Filtrar por categoria `Alimentação`
- **Resultado Esperado:**
  - Apenas produtos de alimentação exibidos
- **Status:** `[x]`

---

### CT-PROD-03 — Filtrar produtos com estoque baixo
- **Pré-condição:** Produto com `estoque_atual < estoque_minimo`
- **Passos:**
  1. Filtrar por `estoque_baixo=1`
- **Resultado Esperado:**
  - Apenas produtos com estoque abaixo do mínimo listados
- **Status:** `[x]`

---

### CT-PROD-04 — Registrar movimentação de entrada
- **Pré-condição:** Produto com `estoque_atual = 50`
- **Passos:**
  1. `?page=produtos&action=movimentacao&id={ID}`
  2. Tipo `entrada`, Quantidade `20`, Motivo `Reposição`
  3. Salvar
- **Resultado Esperado:**
  - `estoque_atual` passa de `50` para `70`
  - Movimentação do tipo `entrada` registrada em `estoque_movimentacoes`
- **Status:** `[x]`

---

### CT-PROD-05 — Registrar movimentação de saída manual
- **Pré-condição:** Produto com `estoque_atual = 70`
- **Passos:**
  1. Tipo `saida`, Quantidade `5`, Motivo `Uso interno`
  2. Salvar
- **Resultado Esperado:**
  - `estoque_atual` passa de `70` para `65`
  - Movimentação do tipo `saida` registrada
- **Status:** `[x]`

---

### CT-PROD-06 — Tentativa de saída com estoque insuficiente
- **Pré-condição:** Produto com `estoque_atual = 5`
- **Passos:**
  1. Registrar saída de quantidade `10`
- **Resultado Esperado:**
  - Operação bloqueada com mensagem de erro OU `estoque_atual` fica negativo (verificar comportamento do sistema)
- **Status:** `[ ]`

---

### CT-PROD-07 — Visualizar histórico de movimentações
- **Pré-condição:** Produto com entradas e saídas registradas
- **Passos:**
  1. `?page=produtos&action=view&id={ID}`
- **Resultado Esperado:**
  - Todas as movimentações (data, tipo, quantidade, motivo) exibidas em ordem cronológica
- **Status:** `[x]`

---

### CT-PROD-08 — Editar produto (preços e mínimos)
- **Pré-condição:** Produto existente
- **Passos:**
  1. Alterar preço de venda para `130.00` e estoque mínimo para `15`
  2. Salvar
- **Resultado Esperado:**
  - Dados atualizados; estoque atual não alterado pela edição
- **Status:** `[x]`

---

### CT-PROD-09 — Deletar produto sem movimentações de venda
- **Pré-condição:** Produto sem histórico de vendas
- **Passos:**
  1. `?page=produtos&action=delete&id={ID}`
- **Resultado Esperado:**
  - Produto removido ou inativado
- **Status:** `[x]`

---

### CT-PROD-10 — Busca AJAX de produtos
- **Pré-condição:** Produtos cadastrados
- **Passos:**
  1. GET `?page=produtos&action=buscar&termo=Ração`
- **Resultado Esperado:**
  - JSON com produtos que contêm "Ração" no nome
- **Status:** `[ ]`

---

## 9. Vendas (PDV)

### CT-VND-01 — Criar venda com 1 produto
- **Pré-condição:** Produto com estoque > 0; módulo `vendas` ativo
- **Passos:**
  1. `?page=vendas&action=create`
  2. Selecionar cliente e pet (opcional)
  3. Adicionar item: Tipo `produto`, Produto `Ração Premium`, Quantidade `2`, Preço `120.00`
  4. Forma pagamento `dinheiro`
  5. Salvar
- **Resultado Esperado:**
  - Venda inserida com `status = 'finalizada'`, `valor_total = 240.00`, `valor_final = 240.00`
  - `estoque_atual` do produto reduzido em 2
  - Movimentação de saída registrada em `estoque_movimentacoes`
  - Lançamento de receita `pago` criado em `financeiro`
- **Status:** `[x]`

---

### CT-VND-02 — Criar venda com múltiplos produtos
- **Pré-condição:** Pelo menos 2 produtos com estoque disponível
- **Passos:**
  1. Adicionar Produto A (qtd 1, R$ 50) + Produto B (qtd 3, R$ 30)
  2. Salvar
- **Resultado Esperado:**
  - `valor_total = 140.00`
  - Estoque de ambos os produtos decrementado corretamente
  - Lançamento financeiro com `valor = 140.00`
- **Status:** `[x]`

---

### CT-VND-03 — Criar venda com serviço
- **Pré-condição:** Serviço ativo cadastrado
- **Passos:**
  1. Adicionar item: Tipo `servico`, Serviço `Banho e Tosa`, Quantidade `1`, Preço `80.00`
  2. Salvar
- **Resultado Esperado:**
  - Venda criada; sem alteração de estoque de produtos
  - Lançamento financeiro registrado
- **Status:** `[x]`

---

### CT-VND-04 — Criar venda com produto e serviço juntos
- **Pré-condição:** Produto e serviço disponíveis
- **Passos:**
  1. Adicionar produto (R$ 50) + serviço (R$ 80)
  2. Salvar
- **Resultado Esperado:**
  - `valor_total = 130.00`
  - Estoque decrementado apenas para o produto
  - Financeiro: lançamento único de R$ 130,00
- **Status:** `[x]`

---

### CT-VND-05 — Criar venda com desconto
- **Pré-condição:** Produto disponível
- **Passos:**
  1. Adicionar produto R$ 100
  2. Desconto: `20.00`
  3. Salvar
- **Resultado Esperado:**
  - `valor_total = 100.00`, `desconto = 20.00`, `valor_final = 80.00`
  - Lançamento financeiro com `valor = 80.00` (valor final, não bruto)
- **Status:** `[x]`

---

### CT-VND-06 — Criar venda sem nenhum item
- **Pré-condição:** Nenhuma
- **Passos:**
  1. Acessar criação de venda
  2. Não adicionar nenhum item
  3. Tentar salvar
- **Resultado Esperado:**
  - Erro `"Adicione pelo menos um item à venda."`
  - Nenhuma venda inserida no banco
- **Status:** `[x]`

---

### CT-VND-07 — Criar venda sem cliente (balcão)
- **Pré-condição:** Produto disponível
- **Passos:**
  1. Deixar cliente e pet em branco
  2. Adicionar produto e salvar
- **Resultado Esperado:**
  - Venda criada com `tutor_id = NULL` e `pet_id = NULL`
- **Status:** `[x]`

---

### CT-VND-08 — Criar venda com profissional associado ao serviço
- **Pré-condição:** Profissional e serviço cadastrados
- **Passos:**
  1. Adicionar serviço e selecionar profissional no item
  2. Salvar
- **Resultado Esperado:**
  - `venda_itens.profissional_id` preenchido corretamente
- **Status:** `[x]`

---

### CT-VND-09 — Visualizar detalhes de venda
- **Pré-condição:** Venda existente com múltiplos itens
- **Passos:**
  1. `?page=vendas&action=view&id={ID}`
- **Resultado Esperado:**
  - Cabeçalho da venda: cliente, data, forma de pagamento, valores
  - Listagem de itens: nome, quantidade, preço unitário, preço total
  - Total da venda calculado corretamente
- **Status:** `[x]`

---

### CT-VND-10 — Cancelar venda (estorno de estoque)
- **Pré-condição:** Venda finalizada com produto de estoque (ex: produto com estoque 8 após a venda de 2 unidades)
- **Passos:**
  1. `?page=vendas&action=cancelar&id={ID}`
- **Resultado Esperado:**
  - Venda com `status = 'cancelada'`
  - Estoque do produto incrementado de volta (8 → 10)
  - Movimentação de entrada por `Cancelamento de venda` registrada
  - Lançamento financeiro com `status = 'cancelado'`
- **Status:** `[x]`

---

### CT-VND-11 — Listar vendas com filtro de período
- **Pré-condição:** Vendas em datas diferentes
- **Passos:**
  1. Filtrar por `data_inicio=2026-03-01&data_fim=2026-03-31`
- **Resultado Esperado:**
  - Apenas vendas de março/2026 exibidas
  - Totalizadores refletem o período filtrado
- **Status:** `[x]`

---

### CT-VND-12 — Filtrar vendas por status
- **Pré-condição:** Vendas finalizadas e canceladas
- **Passos:**
  1. Filtrar por `status=cancelada`
- **Resultado Esperado:**
  - Apenas vendas canceladas listadas
- **Status:** `[x]`

---

### CT-VND-13 — Totalizadores na listagem de vendas
- **Pré-condição:** 3 vendas finalizadas no período: R$ 100, R$ 200, R$ 50
- **Passos:**
  1. Listar vendas do período
- **Resultado Esperado:**
  - `total_vendas = 3`
  - `faturamento_total = R$ 350,00`
  - `ticket_medio = R$ 116,67`
- **Status:** `[x]`

---

### CT-VND-14 — Formas de pagamento diferentes
- **Pré-condição:** Módulo vendas ativo
- **Passos:**
  1. Criar venda com `dinheiro`
  2. Criar venda com `cartao_credito`
  3. Criar venda com `pix`
- **Resultado Esperado:**
  - Cada forma de pagamento salva corretamente
  - Lançamento financeiro reflete a forma de pagamento
- **Status:** `[x]`

---

## 10. Financeiro

### CT-FIN-01 — Criar lançamento de receita
- **Pré-condição:** Módulo `financeiro` ativo
- **Passos:**
  1. `?page=financeiro&action=create`
  2. Tipo `receita`, Categoria `Serviços`, Descrição `Pagamento avulso`, Valor `200.00`, Forma pagamento `dinheiro`, Data `2026-03-11`, Status `pago`
  3. Salvar
- **Resultado Esperado:**
  - Lançamento inserido corretamente
  - Aparece na listagem do período
- **Status:** `[ ]`

---

### CT-FIN-02 — Criar lançamento de despesa
- **Pré-condição:** Módulo `financeiro` ativo
- **Passos:**
  1. Tipo `despesa`, Categoria `Fornecedores`, Descrição `Compra de ração`, Valor `500.00`, Status `pendente`
  2. Salvar
- **Resultado Esperado:**
  - Despesa inserida; `status = 'pendente'`
  - Totalizadores refletem apenas `pago` no saldo
- **Status:** `[ ]`

---

### CT-FIN-03 — Totalizadores receitas vs despesas
- **Pré-condição:** Receitas pagas: R$ 1000; Despesas pagas: R$ 300; 1 despesa pendente: R$ 200
- **Passos:**
  1. Listar financeiro do período
- **Resultado Esperado:**
  - `total_receitas = R$ 1.000,00` (apenas pagas)
  - `total_despesas = R$ 300,00` (apenas pagas)
  - Saldo = R$ 700,00
  - Despesa pendente NÃO entra nos totalizadores
- **Status:** `[ ]`

---

### CT-FIN-04 — Filtrar por tipo (receita/despesa)
- **Pré-condição:** Receitas e despesas cadastradas
- **Passos:**
  1. Filtrar por `tipo=receita`
  2. Filtrar por `tipo=despesa`
- **Resultado Esperado:**
  - Cada filtro retorna somente os lançamentos do tipo
- **Status:** `[ ]`

---

### CT-FIN-05 — Filtrar por status (pago/pendente)
- **Pré-condição:** Lançamentos pagos e pendentes
- **Passos:**
  1. Filtrar por `status=pendente`
- **Resultado Esperado:**
  - Apenas lançamentos pendentes listados
- **Status:** `[ ]`

---

### CT-FIN-06 — Filtrar por período
- **Pré-condição:** Lançamentos em datas diferentes
- **Passos:**
  1. Filtrar `data_inicio=2026-03-01&data_fim=2026-03-11`
- **Resultado Esperado:**
  - Apenas lançamentos dentro do período; totalizadores corretos para o intervalo
- **Status:** `[ ]`

---

### CT-FIN-07 — Deletar lançamento manual
- **Pré-condição:** Lançamento criado manualmente (sem `venda_id`)
- **Passos:**
  1. `?page=financeiro&action=delete&id={ID}`
- **Resultado Esperado:**
  - Lançamento removido do banco; não aparece na lista
- **Status:** `[ ]`

---

### CT-FIN-08 — Lançamento automático por venda aparece no financeiro
- **Pré-condição:** Venda finalizada no dia
- **Passos:**
  1. Criar venda de R$ 150
  2. Ir para `?page=financeiro&action=list`
- **Resultado Esperado:**
  - Lançamento `Venda #ID` com `tipo = 'receita'`, `status = 'pago'`, `valor = 150.00` visível
- **Status:** `[ ]`

---

### CT-FIN-09 — Cancelamento de venda reflete no financeiro
- **Pré-condição:** Venda com lançamento financeiro associado
- **Passos:**
  1. Cancelar a venda
  2. Verificar financeiro
- **Resultado Esperado:**
  - Lançamento associado muda para `status = 'cancelado'`
  - Total de receitas decrementado
- **Status:** `[ ]`

---

### CT-FIN-10 — Deletar lançamento de outra empresa (segurança)
- **Pré-condição:** Duas empresas; lançamento da empresa B
- **Passos:**
  1. Logado como empresa A, tentar `?page=financeiro&action=delete&id={ID_EMPRESA_B}`
- **Resultado Esperado:**
  - Operação não realizada (WHERE com `company_id` correto)
  - Mensagem de erro ou redirecionamento sem deleção
- **Status:** `[ ]`

---

## 11. Relatórios

### CT-REL-01 — Relatório de faturamento por período
- **Pré-condição:** Vendas finalizadas no período; módulo `relatorios` ativo
- **Passos:**
  1. `?page=relatorios&relatorio=faturamento&data_inicio=2026-03-01&data_fim=2026-03-31`
- **Resultado Esperado:**
  - Total de vendas, faturamento bruto, ticket médio do período
  - Lista das vendas com cliente, data, valor
  - Vendas canceladas NÃO contam no total
- **Status:** `[ ]`

---

### CT-REL-02 — Relatório de serviços mais vendidos
- **Pré-condição:** Vendas com serviços no período
- **Passos:**
  1. `?page=relatorios&relatorio=servicos&data_inicio=...&data_fim=...`
- **Resultado Esperado:**
  - Ranking de serviços por quantidade vendida e receita gerada
- **Status:** `[ ]`

---

### CT-REL-03 — Relatório de produtos mais vendidos
- **Pré-condição:** Vendas com produtos no período
- **Passos:**
  1. `?page=relatorios&relatorio=produtos`
- **Resultado Esperado:**
  - Ranking de produtos por quantidade vendida
  - Seção de produtos com estoque baixo
  - Valor total do estoque atual
- **Status:** `[ ]`

---

### CT-REL-04 — Relatório de performance de profissionais
- **Pré-condição:** Vendas com serviços associados a profissionais
- **Passos:**
  1. `?page=relatorios&relatorio=profissionais&data_inicio=...&data_fim=...`
- **Resultado Esperado:**
  - Para cada profissional: total de serviços realizados, receita gerada, comissão calculada
- **Status:** `[ ]`

---

### CT-REL-05 — Relatório de agendamentos por período
- **Pré-condição:** Agendamentos com status variados
- **Passos:**
  1. `?page=relatorios&relatorio=agendamentos&data_inicio=...&data_fim=...`
- **Resultado Esperado:**
  - Lista de agendamentos do período com: pet, tutor, serviço, profissional, data/hora, status
- **Status:** `[ ]`

---

### CT-REL-06 — Relatório sem dados no período
- **Pré-condição:** Nenhum dado no período selecionado
- **Passos:**
  1. Filtrar por período no futuro distante
- **Resultado Esperado:**
  - Relatório exibe "nenhum dado encontrado" sem erros PHP
  - Totalizadores exibem zero
- **Status:** `[ ]`

---

### CT-REL-07 — Relatório sem filtro de data (usa padrão mês atual)
- **Pré-condição:** Nenhuma
- **Passos:**
  1. Acessar `?page=relatorios` sem parâmetros de data
- **Resultado Esperado:**
  - Relatório padrão carregado com data do mês corrente
  - Nenhum erro exibido
- **Status:** `[ ]`

---

## 12. Planos e Assinatura

### CT-PLAN-01 — Visualizar página de planos durante trial
- **Pré-condição:** Empresa com `subscription_status = 'trialing'` e trial vigente
- **Passos:**
  1. Acessar `public/planos.php`
- **Resultado Esperado:**
  - Alerta informando dias restantes do trial (ex: "5 dias restantes")
  - Três planos exibidos com preços
  - Botão "Assinar" disponível para cada plano
- **Status:** `[ ]`

---

### CT-PLAN-02 — Visualizar página de planos com trial expirado
- **Pré-condição:** Empresa com `trial_ends_at` no passado e `subscription_status = 'incomplete'`
- **Passos:**
  1. Acessar `public/planos.php`
- **Resultado Esperado:**
  - Alerta "Trial expirado" exibido
  - Planos disponíveis para contratação
- **Status:** `[ ]`

---

### CT-PLAN-03 — Iniciar checkout Stripe (plano Banho & Tosa)
- **Pré-condição:** Stripe configurado; empresa sem assinatura ativa
- **Passos:**
  1. `public/planos.php`
  2. Clicar "Assinar" no plano Banho & Tosa
  3. POST para `public/subscribe.php` com `plano=banho_tosa`
- **Resultado Esperado:**
  - Customer criado ou reutilizado na Stripe
  - Checkout session gerado
  - Redirecionamento para URL de checkout Stripe
- **Status:** `[ ]`

---

### CT-PLAN-04 — Plano inválido no subscribe
- **Pré-condição:** Nenhuma
- **Passos:**
  1. POST para `subscribe.php` com `plano=inexistente`
- **Resultado Esperado:**
  - Erro: "Plano inválido"
  - Nenhuma chamada à API Stripe
  - Redirecionamento para `planos.php`
- **Status:** `[ ]`

---

### CT-PLAN-05 — Retorno de sucesso do Stripe
- **Pré-condição:** Checkout Stripe concluído com sucesso
- **Passos:**
  1. Acesso a `subscription-success.php` após pagamento
- **Resultado Esperado:**
  - `subscription_status` atualizado para `'active'` no banco
  - Sessão atualizada
  - Redirecionamento para dashboard
- **Status:** `[ ]`

---

### CT-PLAN-06 — Retorno de cancelamento do Stripe
- **Pré-condição:** Usuário abandona checkout Stripe
- **Passos:**
  1. Stripe redireciona para `planos.php?canceled=1`
- **Resultado Esperado:**
  - Mensagem "Assinatura cancelada" ou similar exibida
  - Status da empresa NÃO alterado
- **Status:** `[ ]`

---

### CT-PLAN-07 — Webhook: atualização de assinatura
- **Pré-condição:** Endpoint `webhook.php` acessível
- **Passos:**
  1. Enviar evento `customer.subscription.updated` com novo status
- **Resultado Esperado:**
  - `subscription_status` atualizado no banco conforme o evento
- **Status:** `[ ]`

---

### CT-PLAN-08 — Webhook: cancelamento de assinatura
- **Pré-condição:** Empresa com assinatura ativa
- **Passos:**
  1. Enviar evento `customer.subscription.deleted`
- **Resultado Esperado:**
  - `subscription_status` atualizado para `'canceled'` ou `'incomplete'`
- **Status:** `[ ]`

---

## 13. Multi-tenancy e Isolamento de Dados

### CT-MT-01 — Clientes isolados por empresa
- **Pré-condição:** Empresas A e B com clientes diferentes
- **Passos:**
  1. Logado como empresa A: listar `?page=clientes`
  2. Logado como empresa B: listar `?page=clientes`
- **Resultado Esperado:**
  - Empresa A vê apenas seus clientes
  - Empresa B vê apenas seus clientes
  - Contagens diferentes e corretas
- **Status:** `[ ]`

---

### CT-MT-02 — Pets isolados por empresa
- **Pré-condição:** Empresas A e B com pets diferentes
- **Passos:**
  1. Cada empresa lista seus pets
- **Resultado Esperado:**
  - Nenhuma empresa vê pets da outra
- **Status:** `[ ]`

---

### CT-MT-03 — Vendas isoladas por empresa
- **Pré-condição:** Empresas A e B com vendas diferentes
- **Passos:**
  1. Cada empresa lista vendas; verifica totais
- **Resultado Esperado:**
  - Totais independentes; sem cross-tenant
- **Status:** `[ ]`

---

### CT-MT-04 — Acesso direto a ID de outra empresa
- **Pré-condição:** Pet ID 999 pertencente à empresa B
- **Passos:**
  1. Logado como empresa A, acessar `?page=pets&action=view&id=999`
- **Resultado Esperado:**
  - Mensagem "Pet não encontrado" ou redirecionamento
  - Dados do pet da empresa B NÃO expostos
- **Status:** `[ ]`

---

### CT-MT-05 — Relatórios isolados por empresa
- **Pré-condição:** Vendas em ambas as empresas
- **Passos:**
  1. Relatório de faturamento de cada empresa
- **Resultado Esperado:**
  - Cada empresa vê apenas seu próprio faturamento
- **Status:** `[ ]`

---

### CT-MT-06 — Dashboard com dados isolados
- **Pré-condição:** Duas empresas com volumes diferentes de dados
- **Passos:**
  1. Dashboard de cada empresa
- **Resultado Esperado:**
  - KPIs independentes; nunca somam dados de empresas diferentes
- **Status:** `[ ]`

---

## 14. Controle de Acesso por Plano

### CT-ACL-01 — Módulo agenda bloqueado no plano loja
- **Pré-condição:** Empresa com plano `loja` (Pet Shop)
- **Passos:**
  1. Acessar `?page=agenda`
- **Resultado Esperado:**
  - Redirecionamento para dashboard com mensagem `"Módulo não disponível no seu plano."`
- **Status:** `[ ]`

---

### CT-ACL-02 — Módulo produtos bloqueado no plano banho_tosa
- **Pré-condição:** Empresa com plano `banho_tosa`
- **Passos:**
  1. Acessar `?page=produtos`
- **Resultado Esperado:**
  - Redirecionamento com mensagem de módulo indisponível
- **Status:** `[ ]`

---

### CT-ACL-03 — Módulo financeiro bloqueado no plano banho_tosa
- **Pré-condição:** Empresa com plano `banho_tosa`
- **Passos:**
  1. Acessar `?page=financeiro`
- **Resultado Esperado:**
  - Acesso negado; redirecionamento
- **Status:** `[ ]`

---

### CT-ACL-04 — Plano completo acessa todos os módulos
- **Pré-condição:** Empresa com plano `completo`
- **Passos:**
  1. Acessar agenda, produtos, vendas, financeiro, relatórios
- **Resultado Esperado:**
  - Todos os módulos acessíveis sem mensagem de bloqueio
- **Status:** `[ ]`

---

### CT-ACL-05 — Menu oculta itens indisponíveis no plano
- **Pré-condição:** Empresa com plano `banho_tosa`
- **Passos:**
  1. Observar menu lateral/topo
- **Resultado Esperado:**
  - Itens de módulos não incluídos no plano (`produtos`, `vendas`, `financeiro`) não aparecem no menu ou estão desabilitados
- **Status:** `[ ]`

---

### CT-ACL-06 — Trial com plano completo tem acesso total
- **Pré-condição:** Empresa em período de trial
- **Passos:**
  1. Acessar todos os módulos
- **Resultado Esperado:**
  - Todos os módulos acessíveis durante o trial (plano `completo` por padrão)
- **Status:** `[ ]`

---

## 15. Fluxos Integrados de Ponta a Ponta

### CT-E2E-01 — Fluxo completo: cadastro → agendamento → venda → financeiro
- **Pré-condição:** Empresa com plano `completo`
- **Passos:**
  1. Criar cliente `Ana Silva`
  2. Criar pet `Bolinha` (cão, labrador) para Ana
  3. Criar serviço `Banho e Tosa` (R$ 80)
  4. Criar profissional `Carlos` (comissão 15%)
  5. Agendar `Bolinha` para `Banho e Tosa` com `Carlos` em data futura
  6. Alterar status do agendamento para `finalizado`
  7. Criar venda para `Ana`/`Bolinha`: serviço `Banho e Tosa` com `Carlos`, forma `dinheiro`
  8. Verificar financeiro
  9. Verificar relatório de faturamento
  10. Verificar relatório de profissionais
- **Resultado Esperado:**
  - Todos os passos concluídos sem erro
  - Financeiro: lançamento de R$ 80 `pago`
  - Relatório de faturamento: R$ 80 no período
  - Relatório de profissionais: Carlos com 1 serviço, R$ 80 receita, R$ 12 comissão (15%)
- **Status:** `[ ]`

---

### CT-E2E-02 — Fluxo de venda com estoque: compra → baixa → reposição
- **Pré-condição:** Plano `completo`
- **Passos:**
  1. Criar produto `Shampoo Pet` (estoque: 20, mínimo: 5, preço: R$ 25)
  2. Criar venda com 3 unidades do shampoo
  3. Verificar que estoque = 17
  4. Registrar movimentação de entrada de 10 unidades
  5. Verificar que estoque = 27
  6. Cancelar a venda
  7. Verificar que estoque = 30 (estorno das 3 unidades)
  8. Verificar histórico de movimentações do produto
- **Resultado Esperado:**
  - Cada passo reflete corretamente o saldo de estoque
  - Histórico mostra: entrada inicial (20), saída venda (3), entrada manual (10), entrada cancelamento (3)
  - Total final: 30
- **Status:** `[ ]`

---

### CT-E2E-03 — Fluxo de trial até assinatura
- **Pré-condição:** Nenhuma
- **Passos:**
  1. Cadastrar nova empresa (trial de 7 dias)
  2. Usar o sistema normalmente durante o trial
  3. Assinar plano `loja` via Stripe
  4. Verificar que acesso a `agenda` está bloqueado
  5. Verificar que acesso a `produtos` e `vendas` está liberado
- **Resultado Esperado:**
  - Restrições de módulo aplicadas conforme o novo plano
  - `subscription_status = 'active'`, `plano = 'loja'`
- **Status:** `[ ]`

---

### CT-E2E-04 — Cliente com múltiplos pets e histórico completo
- **Pré-condição:** Plano `completo`
- **Passos:**
  1. Criar cliente `José`
  2. Criar pets `Rex` e `Mimi` para José
  3. Criar 2 agendamentos para Rex e 1 para Mimi
  4. Criar 2 vendas associando José e seus pets
  5. Ver perfil de José (`?page=clientes&action=view`)
- **Resultado Esperado:**
  - 2 pets listados no perfil
  - 2 vendas no histórico
  - 3 agendamentos no histórico
- **Status:** `[ ]`

---

### CT-E2E-05 — Venda com desconto reflete corretamente em todos os módulos
- **Pré-condição:** Plano `completo`
- **Passos:**
  1. Criar venda: produto R$ 200 + serviço R$ 100, desconto R$ 50
  2. `valor_total = 300`, `valor_final = 250`
  3. Verificar financeiro
  4. Verificar relatório de faturamento
- **Resultado Esperado:**
  - Financeiro registra `valor = 250` (valor_final, não total bruto)
  - Relatório de faturamento conta R$ 250 (e não R$ 300)
  - `valor_total = 300`, `desconto = 50`, `valor_final = 250` no banco
- **Status:** `[ ]`

---

### CT-E2E-06 — Verificação de integridade após cancelamento em cascata
- **Pré-condição:** Venda com 2 produtos (3 unidades cada) e lançamento financeiro
- **Passos:**
  1. Estoque antes: Produto A = 10, Produto B = 15
  2. Criar venda: 3x A + 3x B
  3. Estoque após venda: A = 7, B = 12
  4. Cancelar venda
  5. Verificar estoque, financeiro e status da venda
- **Resultado Esperado:**
  - Venda: `status = 'cancelada'`
  - Estoque: A = 10, B = 15 (restaurado)
  - Financeiro: lançamento com `status = 'cancelado'`
  - Movimentações de estoque: saída (venda) + entrada (cancelamento)
  - Toda a operação executada em transação (ACID)
- **Status:** `[ ]`

---

*Fim dos Cenários de Teste — Pawfy v1.0.0*
