# 📋 GUIA DE INSTALAÇÃO RÁPIDA
# Sistema Pet Shop SaaS

## ✅ Checklist de Instalação

### 1. Pré-requisitos
- [ ] PHP 7.4 ou superior instalado
- [ ] MySQL 5.7 ou superior instalado
- [ ] Servidor Apache ou Nginx
- [ ] Extensões PHP necessárias: PDO, pdo_mysql, mbstring, json

### 2. Configuração do Banco de Dados

```bash
# Abra o terminal MySQL
mysql -u root -p

# Execute os comandos SQL ou importe o arquivo
source /caminho/completo/para/database/schema.sql

# Ou usando importação direta
mysql -u root -p < database/schema.sql
```

**Alternativa via phpMyAdmin:**
1. Acesse phpMyAdmin
2. Crie um banco chamado `petshop_saas`
3. Importe o arquivo `database/schema.sql`
4. Verifique se as tabelas foram criadas com sucesso

### 3. Configuração do Sistema

Edite o arquivo: `config/config.php`

```php
// Ajuste as configurações de banco
define('DB_HOST', 'localhost');      // Host do MySQL
define('DB_NAME', 'petshop_saas');   // Nome do banco
define('DB_USER', 'root');            // Usuário MySQL
define('DB_PASS', '');                // Senha MySQL

// Ajuste a URL do sistema
define('APP_URL', 'http://localhost/petshop');
```

### 4. Permissões de Pasta (Linux/Mac)

```bash
# Navegue até a pasta do projeto
cd /caminho/para/petshop

# Configure permissões
chmod -R 755 .
chmod -R 777 public/uploads

# Ou se precisar de mais permissões (desenvolvimento)
chmod -R 777 .
```

### 5. Configuração do Servidor Web

#### Apache (usando .htaccess)
- O arquivo `.htaccess` já está incluído
- Certifique-se de que mod_rewrite está habilitado:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx
Adicione ao seu arquivo de configuração:
```nginx
location /petshop {
    try_files $uri $uri/ /petshop/index.php?$query_string;
}
```

### 6. Teste de Acesso

Abra no navegador:
```
http://localhost/petshop
```

Você será redirecionado para a página de login.

### 7. Login no Sistema

**Credenciais Padrão:**
- E-mail: `admin@petshop.com`
- Senha: `admin123`

**⚠️ IMPORTANTE:** Após o primeiro login, vá em configurações e altere a senha!

### 8. Verificações Pós-Instalação

- [ ] Login funciona corretamente
- [ ] Dashboard carrega sem erros
- [ ] Consegue acessar o menu lateral
- [ ] Pode cadastrar um novo tutor
- [ ] Pode cadastrar um novo pet
- [ ] Sistema aplica isolamento multi-tenant (company_id)

## 🐛 Solução de Problemas Comuns

### Erro: "Unable to connect to database"
**Solução:**
1. Verifique se o MySQL está rodando: `sudo systemctl status mysql`
2. Confirme usuário e senha em `config/config.php`
3. Verifique se o banco `petshop_saas` foi criado

### Erro: "Session failed to start"
**Solução:**
1. Verifique permissões da pasta de sessões do PHP
2. Em Linux: `sudo chmod 733 /var/lib/php/sessions`

### Erro 404 - Página não encontrada
**Solução:**
1. Verifique a configuração de `APP_URL` em `config/config.php`
2. Confirme que está acessando a URL correta
3. Verifique se mod_rewrite está habilitado (Apache)

### Erro: "Class not found"
**Solução:**
1. Verifique se todos os arquivos foram copiados corretamente
2. Confirme que os caminhos em `require_once` estão corretos
3. Verifique permissões de leitura dos arquivos

### Tela em branco ou erro 500
**Solução:**
1. Ative display_errors no PHP para ver os erros
2. Verifique o log de erros do Apache/PHP
3. Confirme que todas as extensões PHP estão instaladas

```php
// Adicione temporariamente no topo do config.php para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📱 Testando Funcionalidades

### Teste 1: Cadastro de Tutor
1. Acesse: Tutores → Novo Tutor
2. Preencha os dados
3. Salve e verifique na listagem

### Teste 2: Cadastro de Pet
1. Acesse: Pets → Novo Pet
2. Selecione o tutor cadastrado
3. Preencha informações do pet
4. Salve e verifique

### Teste 3: Cadastro de Produto
1. Acesse: Produtos → Novo Produto
2. Preencha nome, preço, estoque
3. Salve e veja na listagem

### Teste 4: Agendamento
1. Acesse: Agenda → Novo Agendamento
2. Selecione pet, serviço, profissional
3. Escolha data e hora
4. Confirme o agendamento

### Teste 5: Realizar Venda
1. Acesse: Vendas (PDV)
2. Adicione produtos/serviços
3. Finalize a venda
4. Verifique baixa de estoque automática

## 🔒 Segurança em Produção

Antes de colocar em produção, faça:

- [ ] Altere todas as senhas padrão
- [ ] Desabilite display_errors no PHP
- [ ] Configure HTTPS
- [ ] Ajuste permissões de arquivos (644 para arquivos, 755 para pastas)
- [ ] Configure backup automático do banco
- [ ] Ative logs de erro
- [ ] Revise configurações de segurança no .htaccess

## 📊 Dados Iniciais

O sistema vem com dados pré-cadastrados para teste:

- 1 Empresa (Pet Shop Exemplo)
- 1 Usuário Admin
- 2 Tutores
- 3 Pets
- 6 Serviços
- 4 Produtos
- 3 Profissionais

Você pode manter ou excluir esses dados conforme necessário.

## 📞 Suporte

Se encontrar problemas não listados aqui:

1. Revise o arquivo README.md completo
2. Verifique os logs de erro do PHP e Apache
3. Confirme que todas as dependências estão instaladas
4. Teste em um ambiente limpo

## ✅ Sistema Instalado com Sucesso!

Se todos os testes acima funcionaram, seu sistema está pronto para uso!

Próximos passos:
1. Personalize as configurações da empresa
2. Cadastre seus usuários reais
3. Configure os serviços e produtos do seu pet shop
4. Comece a usar o sistema!

---

**Boa sorte com seu Pet Shop! 🐾**
