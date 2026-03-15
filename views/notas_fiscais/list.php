<?php
$page_title = 'Notas Fiscais';
ob_start();

$badge_status = [
    'pendente'     => 'secondary',
    'processando'  => 'warning',
    'emitida'      => 'success',
    'erro'         => 'danger',
    'cancelada'    => 'secondary',
];
$label_status = [
    'pendente'    => 'Pendente',
    'processando' => 'Processando',
    'emitida'     => 'Emitida',
    'erro'        => 'Erro',
    'cancelada'   => 'Cancelada',
];
?>

<div class="page-header">
    <h2>🧾 Notas Fiscais</h2>
    <a href="?page=notas_fiscais&action=emitir" class="btn btn-primary">+ Emitir Nota Fiscal</a>
</div>

<!-- Filtros -->
<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page"   value="notas_fiscais">
            <input type="hidden" name="action" value="list">

            <label>De: <input type="date" name="data_inicio" class="form-control"
                value="<?= htmlspecialchars($_GET['data_inicio'] ?? date('Y-m-01')) ?>"></label>
            <label>Até: <input type="date" name="data_fim" class="form-control"
                value="<?= htmlspecialchars($_GET['data_fim'] ?? date('Y-m-t')) ?>"></label>

            <select name="cliente_id" class="form-control">
                <option value="">Todos os clientes</option>
                <?php foreach ($tutores as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= (($_GET['cliente_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <?php foreach ($label_status as $k => $v): ?>
                    <option value="<?= $k ?>" <?= (($_GET['status'] ?? '') === $k) ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=notas_fiscais&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<!-- Tabela -->
<div class="card">
    <div class="card-body">
        <?php if (empty($notas)): ?>
            <p class="text-center text-muted">Nenhuma nota fiscal encontrada no período.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nº Nota</th>
                        <th>Venda</th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Data Emissão</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notas as $nf): ?>
                    <tr>
                        <td><?= $nf['id'] ?></td>
                        <td><?= htmlspecialchars($nf['numero_nota'] ?? '—') ?></td>
                        <td>
                            <?php if ($nf['venda_id']): ?>
                                <a href="?page=vendas&action=view&id=<?= $nf['venda_id'] ?>">#<?= $nf['venda_id'] ?></a>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($nf['cliente_nome'] ?? '—') ?></td>
                        <td><?= formatarMoeda($nf['valor'] ?? 0) ?></td>
                        <td><?= $nf['data_emissao'] ? date('d/m/Y', strtotime($nf['data_emissao'])) : '—' ?></td>
                        <td>
                            <span class="badge badge-<?= $badge_status[$nf['status']] ?? 'secondary' ?>">
                                <?= $label_status[$nf['status']] ?? ucfirst($nf['status']) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="?page=notas_fiscais&action=view&id=<?= $nf['id'] ?>"
                               class="btn btn-sm btn-info" title="Ver detalhes">👁️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_paginas > 1): ?>
        <div class="pagination">
            <?php if ($pagina_atual > 1): ?>
                <a href="?<?= $qs_filtros ?>&p=<?= $pagina_atual - 1 ?>" class="btn btn-sm btn-secondary">&laquo; Anterior</a>
            <?php endif; ?>
            <?php for ($i = max(1, $pagina_atual - 2); $i <= min($total_paginas, $pagina_atual + 2); $i++): ?>
                <<?= $i === $pagina_atual ? 'span' : 'a href="?' . $qs_filtros . '&p=' . $i . '"' ?>
                   class="btn btn-sm <?= $i === $pagina_atual ? 'btn-primary' : 'btn-secondary' ?>">
                    <?= $i ?>
                </<?= $i === $pagina_atual ? 'span' : 'a' ?>>
            <?php endfor; ?>
            <?php if ($pagina_atual < $total_paginas): ?>
                <a href="?<?= $qs_filtros ?>&p=<?= $pagina_atual + 1 ?>" class="btn btn-sm btn-secondary">Próxima &raquo;</a>
            <?php endif; ?>
        </div>
        <p style="text-align:center;font-size:.8rem;color:#888;margin-top:6px;">
            <?= $total ?> nota<?= $total !== 1 ? 's' : '' ?> · página <?= $pagina_atual ?> de <?= $total_paginas ?>
        </p>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
