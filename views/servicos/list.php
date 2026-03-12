<?php
$page_title = 'Serviços';
$sort_col   = $_GET['orderby'] ?? 'id';
$sort_dir   = $_GET['order']   ?? 'asc';

$qs_filtros = http_build_query(array_filter([
    'page'      => 'servicos',
    'action'    => 'list',
    'nome'      => $_GET['nome']      ?? '',
    'categoria' => $_GET['categoria'] ?? '',
    'status'    => $_GET['status']    ?? '',
    'orderby'   => $sort_col,
    'order'     => $sort_dir,
], fn($v) => $v !== ''));

ob_start();
?>

<div class="page-header">
    <h2>Serviços</h2>
    <a href="?page=servicos&action=create" class="btn btn-primary">+ Novo Serviço</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page"   value="servicos">
            <input type="hidden" name="action" value="list">
            <input type="text" name="nome" placeholder="Buscar por nome..."
                   value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" class="form-control">
            <select name="categoria" class="form-control">
                <option value="">Todas as categorias</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat['categoria']) ?>"
                    <?= (($_GET['categoria'] ?? '') === $cat['categoria']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['categoria']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <option value="ativo"   <?= (($_GET['status'] ?? '') === 'ativo')   ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= (($_GET['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=servicos&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($servicos)): ?>
            <p class="text-center">Nenhum serviço encontrado.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?= thSort('ID',           'id',           $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Nome',          'nome',         $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Categoria',     'categoria',    $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Preço',         'preco',        $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Duração (min)', 'duracao_media',$sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Status',        'status',       $sort_col, $sort_dir) ?></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicos as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['nome']) ?></td>
                    <td><?= htmlspecialchars($s['categoria'] ?? '-') ?></td>
                    <td><?= formatarMoeda($s['preco']) ?></td>
                    <td><?= $s['duracao_media'] ? $s['duracao_media'] . ' min' : '-' ?></td>
                    <td>
                        <span class="badge badge-<?= $s['status'] === 'ativo' ? 'success' : 'danger' ?>">
                            <?= ucfirst($s['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?page=servicos&action=edit&id=<?= $s['id'] ?>"
                           class="btn btn-sm btn-warning" title="Editar">✏️</a>
                        <a href="?page=servicos&action=delete&id=<?= $s['id'] ?>"
                           class="btn btn-sm btn-danger" title="Excluir"
                           onclick="return confirm('Tem certeza que deseja excluir este serviço?')">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_paginas > 1): ?>
        <div class="pagination" style="display:flex;gap:4px;justify-content:center;margin-top:16px;flex-wrap:wrap;">
            <?php if ($pagina_atual > 1): ?>
                <a href="?<?= $qs_filtros ?>&p=<?= $pagina_atual - 1 ?>" class="btn btn-sm btn-secondary">&laquo; Anterior</a>
            <?php endif; ?>

            <?php
            $inicio = max(1, $pagina_atual - 2);
            $fim    = min($total_paginas, $pagina_atual + 2);
            if ($inicio > 1): ?>
                <a href="?<?= $qs_filtros ?>&p=1" class="btn btn-sm btn-secondary">1</a>
                <?php if ($inicio > 2): ?><span style="padding:4px 2px;">…</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                <?php if ($i === $pagina_atual): ?>
                    <span class="btn btn-sm btn-primary"><?= $i ?></span>
                <?php else: ?>
                    <a href="?<?= $qs_filtros ?>&p=<?= $i ?>" class="btn btn-sm btn-secondary"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($fim < $total_paginas): ?>
                <?php if ($fim < $total_paginas - 1): ?><span style="padding:4px 2px;">…</span><?php endif; ?>
                <a href="?<?= $qs_filtros ?>&p=<?= $total_paginas ?>" class="btn btn-sm btn-secondary"><?= $total_paginas ?></a>
            <?php endif; ?>

            <?php if ($pagina_atual < $total_paginas): ?>
                <a href="?<?= $qs_filtros ?>&p=<?= $pagina_atual + 1 ?>" class="btn btn-sm btn-secondary">Próxima &raquo;</a>
            <?php endif; ?>
        </div>
        <p style="text-align:center;font-size:.8rem;color:#888;margin-top:6px;">
            Exibindo <?= count($servicos) ?> de <?= $total ?> serviço<?= $total !== 1 ? 's' : '' ?>
        </p>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
