<?php
$page_title = 'NFS-e #' . $nota['id'];
ob_start();

$badge_status = [
    'pendente'    => 'secondary',
    'processando' => 'warning',
    'emitida'     => 'success',
    'erro'        => 'danger',
    'cancelada'   => 'secondary',
];
$label_status = [
    'pendente'    => 'Pendente',
    'processando' => 'Processando',
    'emitida'     => 'Emitida',
    'erro'        => 'Erro',
    'cancelada'   => 'Cancelada',
];
$status = $nota['status'];
?>

<div class="page-header">
    <h2>🧾 Nota Fiscal #<?= $nota['id'] ?>
        <span class="badge badge-<?= $badge_status[$status] ?? 'secondary' ?>" style="font-size:.75em;vertical-align:middle;">
            <?= $label_status[$status] ?? ucfirst($status) ?>
        </span>
    </h2>
    <div>
        <?php if (!empty($nota['ref_externa']) && in_array($status, ['pendente', 'processando', 'erro'])): ?>
        <a href="?page=notas_fiscais&action=consultar&id=<?= $nota['id'] ?>"
           class="btn btn-info">🔄 Consultar Status</a>
        <?php endif; ?>

        <?php if ($status === 'emitida'): ?>
        <a href="?page=notas_fiscais&action=cancelar&id=<?= $nota['id'] ?>"
           class="btn btn-danger"
           onclick="return confirm('Cancelar esta nota fiscal? Esta ação é irreversível.')">✖️ Cancelar NF</a>
        <?php endif; ?>

        <a href="?page=notas_fiscais&action=list" class="btn btn-secondary">← Voltar</a>
    </div>
</div>

<div class="row">
    <!-- Dados da NF -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3>📄 Dados da Nota Fiscal</h3></div>
            <div class="card-body">
                <table class="table table-detail">
                    <tr><th width="45%">Número:</th>
                        <td><strong><?= htmlspecialchars($nota['numero_nota'] ?? '—') ?></strong></td></tr>
                    <tr><th>Cód. Verificação:</th>
                        <td><?= htmlspecialchars($nota['codigo_verificacao'] ?? '—') ?></td></tr>
                    <tr><th>Status:</th>
                        <td><span class="badge badge-<?= $badge_status[$status] ?? 'secondary' ?>">
                            <?= $label_status[$status] ?? ucfirst($status) ?></span></td></tr>
                    <tr><th>Valor:</th>
                        <td><strong><?= formatarMoeda($nota['valor'] ?? 0) ?></strong></td></tr>
                    <tr><th>Data Emissão:</th>
                        <td><?= $nota['data_emissao'] ? date('d/m/Y', strtotime($nota['data_emissao'])) : '—' ?></td></tr>
                    <tr><th>Criado em:</th>
                        <td><?= date('d/m/Y H:i', strtotime($nota['created_at'])) ?></td></tr>
                    <?php if (!empty($nota['ref_externa'])): ?>
                    <tr><th>Ref. Provedor:</th>
                        <td><small class="text-muted"><?= htmlspecialchars($nota['ref_externa']) ?></small></td></tr>
                    <?php endif; ?>
                    <?php if ($nota['venda_id']): ?>
                    <tr><th>Venda Vinculada:</th>
                        <td><a href="?page=vendas&action=view&id=<?= $nota['venda_id'] ?>">Venda #<?= $nota['venda_id'] ?></a></td></tr>
                    <?php endif; ?>
                </table>

                <?php if (!empty($nota['pdf_url'])): ?>
                <div style="margin-top:16px;">
                    <a href="<?= htmlspecialchars($nota['pdf_url']) ?>" target="_blank"
                       class="btn btn-primary">📥 Visualizar / Baixar PDF</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Dados do Tomador -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3>👤 Tomador</h3></div>
            <div class="card-body">
                <table class="table table-detail">
                    <tr><th width="45%">Nome:</th>
                        <td><?= htmlspecialchars($nota['cliente_nome'] ?? '—') ?></td></tr>
                    <tr><th>CPF:</th>
                        <td><?= htmlspecialchars($nota['cliente_cpf'] ?? '—') ?></td></tr>
                    <tr><th>E-mail:</th>
                        <td><?= htmlspecialchars($nota['cliente_email'] ?? '—') ?></td></tr>
                    <tr><th>Telefone:</th>
                        <td><?= htmlspecialchars($nota['cliente_telefone'] ?? '—') ?></td></tr>
                </table>
                <?php if ($nota['cliente_id']): ?>
                <a href="?page=clientes&action=view&id=<?= $nota['cliente_id'] ?>" class="btn btn-sm btn-info">
                    Ver perfil do cliente
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Serviço -->
<div class="card">
    <div class="card-header"><h3>🔧 Serviço Prestado</h3></div>
    <div class="card-body">
        <?php if (!empty($nota['descricao_servico'])): ?>
            <p><?= nl2br(htmlspecialchars($nota['descricao_servico'])) ?></p>
        <?php endif; ?>

        <?php if (!empty($itens_venda)): ?>
        <div class="table-responsive" style="margin-top:12px;">
            <table class="table table-sm">
                <thead>
                    <tr><th>Item</th><th>Qtd</th><th>Unit.</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($itens_venda as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome_item']) ?></td>
                        <td><?= $item['quantidade'] ?></td>
                        <td><?= formatarMoeda($item['preco_unitario']) ?></td>
                        <td><?= formatarMoeda($item['preco_total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Mensagem de erro -->
<?php if (!empty($nota['mensagem_erro'])): ?>
<div class="alert alert-error">
    <strong>Erro da API:</strong> <?= htmlspecialchars($nota['mensagem_erro']) ?>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
