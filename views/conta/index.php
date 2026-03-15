<?php
/**
 * View: Minha Conta
 * Abas: Perfil, Senha e Dados Fiscais
 */
?>

<div class="page-header">
    <h2>Minha Conta</h2>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul style="margin:0; padding-left:18px;">
        <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Tabs -->
<div class="tabs" style="margin-bottom:24px;">
    <a href="?page=conta&tab=perfil"
       class="tab-link <?= $tab === 'perfil' ? 'active' : '' ?>">Perfil</a>
    <a href="?page=conta&tab=senha"
       class="tab-link <?= $tab === 'senha'  ? 'active' : '' ?>">Alterar Senha</a>
    <?php if (moduloDisponivel('nota_fiscal')): ?>
    <a href="?page=conta&tab=fiscal"
       class="tab-link <?= $tab === 'fiscal' ? 'active' : '' ?>">🧾 Dados Fiscais</a>
    <?php endif; ?>
</div>

<!-- Tab: Perfil -->
<?php if ($tab === 'perfil'): ?>
<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <input type="hidden" name="acao" value="perfil">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required
                           value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Perfil</label>
                    <input type="text" class="form-control" disabled
                           value="<?= ucfirst(htmlspecialchars($usuario['perfil'] ?? '')) ?>">
                </div>

                <div class="form-group col-md-4">
                    <label>Membro desde</label>
                    <input type="text" class="form-control" disabled
                           value="<?= date('d/m/Y', strtotime($usuario['data_criacao'] ?? 'now')) ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
        </form>
    </div>
</div>

<!-- Tab: Senha -->
<?php elseif ($tab === 'senha'): ?>
<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <input type="hidden" name="acao" value="senha">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="senha_atual">Senha atual *</label>
                    <input type="password" id="senha_atual" name="senha_atual" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nova_senha">Nova senha * <small>(mínimo 8 caracteres)</small></label>
                    <input type="password" id="nova_senha" name="nova_senha" class="form-control" required minlength="8">
                </div>

                <div class="form-group col-md-6">
                    <label for="confirmar_senha">Confirmar nova senha *</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required minlength="8">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Alterar senha</button>
            </div>
        </form>
    </div>
</div>

<!-- Tab: Dados Fiscais -->
<?php elseif ($tab === 'fiscal' && moduloDisponivel('nota_fiscal')): ?>
<div class="card">
    <div class="card-header">
        <h3>🧾 Configurações Fiscais — NFS-e</h3>
    </div>
    <div class="card-body">
        <p class="text-muted" style="margin-bottom:20px;font-size:.88rem;">
            Preencha os dados do prestador de serviços (seu pet shop) utilizados na emissão das Notas Fiscais de Serviço eletrônicas.
            O token da API é obtido diretamente no painel do provedor NFS-e (ex: Focus NFe).
        </p>
        <form method="POST" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <input type="hidden" name="acao" value="fiscal">

            <h4 style="margin-bottom:12px;font-size:15px;color:#374151;">Dados do Prestador</h4>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="cnpj">CNPJ *</label>
                    <input type="text" id="cnpj" name="cnpj" class="form-control"
                           placeholder="00.000.000/0001-00"
                           value="<?= htmlspecialchars($config_fiscal['cnpj'] ?? '') ?>">
                </div>
                <div class="form-group col-md-8">
                    <label for="razao_social">Razão Social *</label>
                    <input type="text" id="razao_social" name="razao_social" class="form-control"
                           value="<?= htmlspecialchars($config_fiscal['razao_social'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inscricao_municipal">Inscrição Municipal *</label>
                    <input type="text" id="inscricao_municipal" name="inscricao_municipal" class="form-control"
                           value="<?= htmlspecialchars($config_fiscal['inscricao_municipal'] ?? '') ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="codigo_municipio">Código IBGE do Município *</label>
                    <input type="text" id="codigo_municipio" name="codigo_municipio" class="form-control"
                           placeholder="Ex: 4119905 (Paranaguá/PR)"
                           value="<?= htmlspecialchars($config_fiscal['codigo_municipio'] ?? '') ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="municipio">Município</label>
                    <input type="text" id="municipio" name="municipio" class="form-control"
                           value="<?= htmlspecialchars($config_fiscal['municipio'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="logradouro">Logradouro</label>
                    <input type="text" id="logradouro" name="logradouro" class="form-control"
                           value="<?= htmlspecialchars($config_fiscal['logradouro'] ?? '') ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="numero_endereco">Número</label>
                    <input type="text" id="numero_endereco" name="numero_endereco" class="form-control"
                           value="<?= htmlspecialchars($config_fiscal['numero_endereco'] ?? '') ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" class="form-control"
                           value="<?= htmlspecialchars($config_fiscal['bairro'] ?? '') ?>">
                </div>
                <div class="form-group col-md-1">
                    <label for="uf">UF</label>
                    <input type="text" id="uf" name="uf" class="form-control" maxlength="2"
                           value="<?= htmlspecialchars($config_fiscal['uf'] ?? '') ?>">
                </div>
                <div class="form-group col-md-1">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" class="form-control"
                           value="<?= htmlspecialchars($config_fiscal['cep'] ?? '') ?>">
                </div>
            </div>

            <h4 style="margin:20px 0 12px;font-size:15px;color:#374151;">Configuração Tributária</h4>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="codigo_servico">
                        Cód. Serviço (LC 116) *
                        <small class="text-muted" style="font-size:.75em;display:block;">Ex: 14.01 — Lubrificação, limpeza...</small>
                    </label>
                    <input type="text" id="codigo_servico" name="codigo_servico" class="form-control"
                           placeholder="14.01"
                           value="<?= htmlspecialchars($config_fiscal['codigo_servico'] ?? '') ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="codigo_tributario_municipio">Cód. Tributário Municipal</label>
                    <input type="text" id="codigo_tributario_municipio" name="codigo_tributario_municipio" class="form-control"
                           value="<?= htmlspecialchars($config_fiscal['codigo_tributario_municipio'] ?? '') ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="aliquota_iss">Alíquota ISS (%)</label>
                    <input type="number" id="aliquota_iss" name="aliquota_iss" class="form-control"
                           step="0.01" min="0" max="5"
                           placeholder="5.00"
                           value="<?= number_format(($config_fiscal['aliquota_iss'] ?? 0.05) * 100, 2, '.', '') ?>">
                    <small class="text-muted">Digite 5 para 5%</small>
                </div>
            </div>

            <h4 style="margin:20px 0 12px;font-size:15px;color:#374151;">Integração com Provedor NFS-e</h4>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="nfse_provedor">Provedor</label>
                    <select id="nfse_provedor" name="nfse_provedor" class="form-control">
                        <option value="focusnfe" <?= ($config_fiscal['nfse_provedor'] ?? 'focusnfe') === 'focusnfe' ? 'selected' : '' ?>>
                            Focus NFe
                        </option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="nfse_ambiente">Ambiente</label>
                    <select id="nfse_ambiente" name="nfse_ambiente" class="form-control">
                        <option value="homologacao" <?= ($config_fiscal['nfse_ambiente'] ?? 'homologacao') === 'homologacao' ? 'selected' : '' ?>>
                            Homologação (testes)
                        </option>
                        <option value="producao"    <?= ($config_fiscal['nfse_ambiente'] ?? '') === 'producao' ? 'selected' : '' ?>>
                            Produção
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="nfse_api_token">Token da API *</label>
                <input type="password" id="nfse_api_token" name="nfse_api_token" class="form-control"
                       placeholder="Token gerado no painel do provedor NFS-e"
                       value="<?= htmlspecialchars($config_fiscal['nfse_api_token'] ?? '') ?>"
                       autocomplete="new-password">
                <small class="text-muted">
                    Obtenha o token no painel da
                    <a href="https://focusnfe.com.br" target="_blank" rel="noopener">Focus NFe</a>.
                    Cada empresa possui seu próprio token.
                </small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar Dados Fiscais</button>
                <?php if (!empty($config_fiscal['nfse_api_token'])): ?>
                <a href="?page=notas_fiscais&action=list" class="btn btn-secondary">Ver Notas Fiscais</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<style>
.tabs {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0;
}
.tab-link {
    padding: 8px 20px;
    text-decoration: none;
    color: #6b7280;
    font-weight: 500;
    border-radius: 6px 6px 0 0;
    border: 1px solid transparent;
    border-bottom: none;
    position: relative;
    bottom: -2px;
    transition: color .15s;
}
.tab-link:hover { color: #2563EB; }
.tab-link.active {
    color: #2563EB;
    background: #fff;
    border-color: #e5e7eb;
    border-bottom-color: #fff;
}
</style>
