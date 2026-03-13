<?php
$page_title = 'Vendas';

// URL de retorno com todos os filtros/página atuais
$list_params = array_diff_key($_GET, ['return_url' => '']);
$return_url  = urlencode('?' . http_build_query($list_params));

ob_start();
?>

<div class="page-header">
    <h2>Vendas (PDV)</h2>
    <a href="?page=vendas&action=create" class="btn btn-primary">+ Nova Venda</a>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page"   value="vendas">
            <input type="hidden" name="action" value="list">
            <label>De: <input type="date" name="data_inicio" value="<?= htmlspecialchars($_GET['data_inicio'] ?? date('Y-m-01')) ?>" class="form-control"></label>
            <label>Até: <input type="date" name="data_fim"   value="<?= htmlspecialchars($_GET['data_fim']    ?? date('Y-m-t'))  ?>" class="form-control"></label>
            <input type="text" name="cliente" placeholder="Buscar por cliente..." value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>" class="form-control">
            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <option value="finalizada" <?= (($_GET['status'] ?? '') == 'finalizada') ? 'selected' : '' ?>>Finalizada</option>
                <option value="cancelada"  <?= (($_GET['status'] ?? '') == 'cancelada')  ? 'selected' : '' ?>>Cancelada</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=vendas&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<?php if (!empty($resumo)): ?>
<div class="dashboard-cards" style="margin-bottom:1rem;">
    <div class="card card-success">
        <div class="card-icon">💰</div>
        <div class="card-content">
            <h3>Faturamento do Período</h3>
            <p class="value"><?= formatarMoeda($resumo['faturamento_total'] ?? 0) ?></p>
            <small><?= $resumo['total_vendas'] ?? 0 ?> vendas | Ticket médio: <?= formatarMoeda($resumo['ticket_medio'] ?? 0) ?></small>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($vendas)): ?>
            <p class="text-center">Nenhuma venda encontrada no período.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Desconto</th>
                    <th>Total</th>
                    <th>Forma Pgto</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $v): ?>
                <tr>
                    <td>#<?= $v['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['data'])) ?></td>
                    <td><?= htmlspecialchars($v['tutor_nome'] ?? '-') ?></td>
                    <td><?= formatarMoeda($v['valor_total']) ?></td>
                    <td><?= $v['desconto'] > 0 ? formatarMoeda($v['desconto']) : '-' ?></td>
                    <td><strong><?= formatarMoeda($v['valor_final']) ?></strong></td>
                    <td><?= ucfirst($v['forma_pagamento']) ?></td>
                    <td>
                        <span class="badge badge-<?= $v['status'] == 'finalizada' ? 'success' : 'danger' ?>">
                            <?= ucfirst($v['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?page=vendas&action=view&id=<?= $v['id'] ?>&return_url=<?= $return_url ?>" class="btn btn-sm btn-info" title="Ver detalhes">👁️</a>
                        <?php if ($v['status'] == 'finalizada'): ?>
                        <a href="?page=vendas&action=cancelar&id=<?= $v['id'] ?>&return_url=<?= $return_url ?>" class="btn btn-sm btn-danger" title="Cancelar" onclick="return confirm('Cancelar esta venda? O estoque será revertido.')">✖️</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_paginas > 1): ?>
        <div class="pagination" style="display:flex;gap:6px;justify-content:center;margin-top:16px;flex-wrap:wrap;">
            <?php
            $nav_params = array_diff_key($_GET, ['pg' => '']);

            if ($pagina_atual > 1):
                $nav_params['pg'] = $pagina_atual - 1; ?>
                <a href="?<?= http_build_query($nav_params) ?>" class="btn btn-sm btn-secondary">‹ Anterior</a>
            <?php endif; ?>

            <?php
            $inicio = max(1, $pagina_atual - 2);
            $fim    = min($total_paginas, $pagina_atual + 2);
            if ($inicio > 1): $nav_params['pg'] = 1; ?>
                <a href="?<?= http_build_query($nav_params) ?>" class="btn btn-sm btn-secondary">1</a>
                <?php if ($inicio > 2): ?><span style="align-self:center">…</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($p = $inicio; $p <= $fim; $p++):
                $nav_params['pg'] = $p; ?>
                <a href="?<?= http_build_query($nav_params) ?>"
                   class="btn btn-sm <?= $p == $pagina_atual ? 'btn-primary' : 'btn-secondary' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>

            <?php if ($fim < $total_paginas):
                if ($fim < $total_paginas - 1): ?><span style="align-self:center">…</span><?php endif;
                $nav_params['pg'] = $total_paginas; ?>
                <a href="?<?= http_build_query($nav_params) ?>" class="btn btn-sm btn-secondary"><?= $total_paginas ?></a>
            <?php endif; ?>

            <?php if ($pagina_atual < $total_paginas):
                $nav_params['pg'] = $pagina_atual + 1; ?>
                <a href="?<?= http_build_query($nav_params) ?>" class="btn btn-sm btn-secondary">Próximo ›</a>
            <?php endif; ?>

            <span style="align-self:center;color:#64748b;font-size:.85rem;">
                <?= $total ?> venda<?= $total != 1 ? 's' : '' ?> · página <?= $pagina_atual ?> de <?= $total_paginas ?>
            </span>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
