<?php
$page_title = 'Clientes';
$sort_col = $_GET['orderby'] ?? 'id';
$sort_dir = $_GET['order'] ?? 'asc';

// Monta query string base preservando filtros ativos (sem 'p')
$qs_filtros = http_build_query(array_filter([
    'page'        => 'clientes',
    'action'      => 'list',
    'nome'        => $_GET['nome']        ?? '',
    'status'      => $_GET['status']      ?? '',
    'com_vinculo' => $_GET['com_vinculo'] ?? '',
    'orderby'     => $sort_col,
    'order'       => $sort_dir,
], fn($v) => $v !== ''));

ob_start();
?>

<div class="page-header">
    <h2>Clientes</h2>
    <a href="?page=clientes&action=create" class="btn btn-primary">+ Novo Cliente</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="form-inline">
            <input type="hidden" name="page"   value="clientes">
            <input type="hidden" name="action" value="list">
            <input type="text" name="nome" placeholder="Buscar por nome..."
                   value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" class="form-control">
            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <option value="ativo"   <?= (($_GET['status'] ?? '') === 'ativo')   ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= (($_GET['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
            </select>

            <label class="switch-label" style="display:flex;align-items:center;gap:6px;margin:0 8px;">
                <?php
                $cv = $_GET['com_vinculo'] ?? '';
                $checked_sim = $cv === '1' ? 'checked' : '';
                $checked_nao = $cv === '0' ? 'checked' : '';
                ?>
                <span style="font-size:.85rem;color:#666;">Vínculo:</span>
                <label style="display:flex;align-items:center;gap:4px;font-size:.85rem;cursor:pointer;">
                    <input type="radio" name="com_vinculo" value="1" <?= $checked_sim ?>> Com vínculo
                </label>
                <label style="display:flex;align-items:center;gap:4px;font-size:.85rem;cursor:pointer;">
                    <input type="radio" name="com_vinculo" value="0" <?= $checked_nao ?>> Sem vínculo
                </label>
                <label style="display:flex;align-items:center;gap:4px;font-size:.85rem;cursor:pointer;">
                    <input type="radio" name="com_vinculo" value=""  <?= $cv === '' ? 'checked' : '' ?>> Todos
                </label>
            </label>

            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=clientes&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($tutores)): ?>
            <p class="text-center">Nenhum cliente encontrado.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?= thSort('ID',       'id',       $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Nome',     'nome',     $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Telefone', 'telefone', $sort_col, $sort_dir) ?></th>
                    <th>WhatsApp</th>
                    <th><?= thSort('Email',    'email',    $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Status',   'status',   $sort_col, $sort_dir) ?></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tutores as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['nome']) ?></td>
                    <td><?= htmlspecialchars($t['telefone']) ?></td>
                    <td><?= htmlspecialchars($t['whatsapp']) ?></td>
                    <td><?= htmlspecialchars($t['email']) ?></td>
                    <td>
                        <span class="badge badge-<?= $t['status'] === 'ativo' ? 'success' : 'danger' ?>">
                            <?= ucfirst($t['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?page=clientes&action=view&id=<?= $t['id'] ?>" class="btn btn-sm btn-info"    title="Visualizar">👁️</a>
                        <a href="?page=clientes&action=edit&id=<?= $t['id'] ?>" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                        <a href="?page=clientes&action=delete&id=<?= $t['id'] ?>"
                           class="btn btn-sm btn-danger" title="Excluir"
                           onclick="return confirm('Tem certeza que deseja excluir este cliente?')">🗑️</a>
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
            Exibindo <?= count($tutores) ?> de <?= $total ?> cliente<?= $total !== 1 ? 's' : '' ?>
        </p>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
