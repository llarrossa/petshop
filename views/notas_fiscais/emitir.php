<?php
$page_title = 'Emitir Nota Fiscal';
ob_start();

$valor_pre    = $venda ? number_format($venda['valor_final'], 2, '.', '') : '';
$data_pre     = $venda ? date('Y-m-d', strtotime($venda['data'])) : date('Y-m-d');
$cpf_pre      = $tutor['cpf']   ?? '';
$email_pre    = $tutor['email'] ?? '';
$nome_pre     = $tutor['nome']  ?? '';
$end_pre      = $tutor['endereco'] ?? '';
$uf_pre       = $tutor['estado']   ?? '';
$cep_pre      = $tutor['cep']      ?? '';
?>

<div class="page-header">
    <h2>🧾 Emitir Nota Fiscal de Serviço</h2>
    <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">← Voltar</a>
</div>

<?php if (!empty($erros)): ?>
<div class="alert alert-error">
    <ul style="margin:0;padding-left:18px;">
        <?php foreach ($erros as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (empty($config_fiscal)): ?>
<div class="alert alert-error">
    ⚠️ Dados fiscais não configurados.
    <a href="?page=conta&tab=fiscal" class="btn btn-sm btn-primary" style="margin-left:12px;">
        Configurar agora
    </a>
</div>
<?php endif; ?>

<?php if ($venda): ?>
<div class="card" style="border-left:4px solid #2563EB;">
    <div class="card-body" style="display:flex;gap:24px;flex-wrap:wrap;align-items:center;">
        <div><strong>Venda #<?= $venda['id'] ?></strong></div>
        <div>Cliente: <strong><?= htmlspecialchars($venda['tutor_nome'] ?? 'Avulso') ?></strong></div>
        <div>Valor: <strong><?= formatarMoeda($venda['valor_final']) ?></strong></div>
        <div>Data: <?= date('d/m/Y', strtotime($venda['data'])) ?></div>
        <a href="?page=vendas&action=view&id=<?= $venda['id'] ?>" class="btn btn-sm btn-info">Ver venda</a>
    </div>
</div>
<?php endif; ?>

<form method="POST" class="needs-validation" novalidate>
    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
    <?php if ($venda_id): ?>
    <input type="hidden" name="venda_id" value="<?= $venda_id ?>">
    <?php endif; ?>

    <div class="row">
        <!-- TOMADOR -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3>👤 Dados do Tomador</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nome_tomador">Nome / Razão Social *</label>
                        <input type="text" name="nome_tomador" id="nome_tomador" class="form-control" required
                               value="<?= htmlspecialchars($_POST['nome_tomador'] ?? $nome_pre) ?>">
                    </div>
                    <div class="form-group">
                        <label for="cpf_cnpj_tomador">CPF ou CNPJ *</label>
                        <input type="text" name="cpf_cnpj_tomador" id="cpf_cnpj_tomador" class="form-control" required
                               placeholder="Somente números"
                               value="<?= htmlspecialchars($_POST['cpf_cnpj_tomador'] ?? $cpf_pre) ?>">
                    </div>
                    <div class="form-group">
                        <label for="email_tomador">E-mail</label>
                        <input type="email" name="email_tomador" id="email_tomador" class="form-control"
                               value="<?= htmlspecialchars($_POST['email_tomador'] ?? $email_pre) ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="endereco_tomador">Endereço</label>
                            <input type="text" name="endereco_tomador" id="endereco_tomador" class="form-control"
                                   value="<?= htmlspecialchars($_POST['endereco_tomador'] ?? $end_pre) ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="uf_tomador">UF</label>
                            <input type="text" name="uf_tomador" id="uf_tomador" class="form-control"
                                   maxlength="2" placeholder="PR"
                                   value="<?= htmlspecialchars($_POST['uf_tomador'] ?? $uf_pre) ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="cep_tomador">CEP</label>
                            <input type="text" name="cep_tomador" id="cep_tomador" class="form-control"
                                   value="<?= htmlspecialchars($_POST['cep_tomador'] ?? $cep_pre) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SERVIÇO -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3>🔧 Serviço e Valor</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="descricao_servico">Descrição do Serviço *</label>
                        <textarea name="descricao_servico" id="descricao_servico" class="form-control"
                                  rows="4" required
                                  placeholder="Descreva o serviço prestado..."><?= htmlspecialchars($_POST['descricao_servico'] ?? $descricao_auto) ?></textarea>
                        <div class="invalid-feedback">A descrição é obrigatória.</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="valor">Valor (R$) *</label>
                            <input type="number" name="valor" id="valor" class="form-control"
                                   step="0.01" min="0.01" required
                                   value="<?= htmlspecialchars($_POST['valor'] ?? $valor_pre) ?>">
                            <div class="invalid-feedback">Informe o valor.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="data_emissao">Data do Serviço *</label>
                            <input type="date" name="data_emissao" id="data_emissao" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['data_emissao'] ?? $data_pre) ?>">
                        </div>
                    </div>

                    <?php if (!empty($config_fiscal)): ?>
                    <div class="alert alert-success" style="font-size:.82rem;margin-top:8px;padding:10px 14px;">
                        <strong>Prestador:</strong> <?= htmlspecialchars($config_fiscal['razao_social'] ?? '') ?><br>
                        <strong>CNPJ:</strong> <?= htmlspecialchars($config_fiscal['cnpj'] ?? '—') ?> &nbsp;
                        <strong>Insc. Municipal:</strong> <?= htmlspecialchars($config_fiscal['inscricao_municipal'] ?? '—') ?><br>
                        <strong>Cód. Serviço:</strong> <?= htmlspecialchars($config_fiscal['codigo_servico'] ?? '—') ?> &nbsp;
                        <strong>ISS:</strong> <?= number_format(($config_fiscal['aliquota_iss'] ?? 0.05) * 100, 2) ?>%<br>
                        <strong>Ambiente:</strong>
                        <span class="badge badge-<?= ($config_fiscal['nfse_ambiente'] ?? '') === 'producao' ? 'success' : 'warning' ?>">
                            <?= ucfirst($config_fiscal['nfse_ambiente'] ?? 'homologação') ?>
                        </span>
                        <a href="?page=conta&tab=fiscal" style="float:right;font-size:.8rem;">Editar dados fiscais</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary" <?= empty($config_fiscal) ? 'disabled' : '' ?>>
            🚀 Emitir Nota Fiscal
        </button>
        <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
(function () {
    'use strict';
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
            form.classList.add('was-validated');
        });
    });
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
