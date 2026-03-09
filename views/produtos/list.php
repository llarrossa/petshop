<?php
$page_title = 'Produtos';
$sort_col = $_GET['orderby'] ?? 'id';
$sort_dir = $_GET['order'] ?? 'asc';
ob_start();
?>

<div class="page-header">
    <h2>Produtos</h2>
    <a href="?page=produtos&action=create" class="btn btn-primary">+ Novo Produto</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page" value="produtos">
            <input type="hidden" name="action" value="list">
            <input type="text" name="nome" placeholder="Buscar por nome..." value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" class="form-control">
            <input type="text" name="sku" placeholder="SKU..." value="<?= htmlspecialchars($_GET['sku'] ?? '') ?>" class="form-control">
            <select name="categoria" class="form-control">
                <option value="">Todas as categorias</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat['categoria']) ?>" <?= (isset($_GET['categoria']) && $_GET['categoria'] == $cat['categoria']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['categoria']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <option value="ativo" <?= (isset($_GET['status']) && $_GET['status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= (isset($_GET['status']) && $_GET['status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>
            </select>
            <label class="form-check-label">
                <input type="checkbox" name="estoque_baixo" value="1" <?= (isset($_GET['estoque_baixo']) && $_GET['estoque_baixo'] == '1') ? 'checked' : '' ?>>
                Estoque baixo
            </label>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=produtos&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($produtos)): ?>
            <p class="text-center">Nenhum produto cadastrado.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?= thSort('ID', 'id', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Nome', 'nome', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('SKU', 'sku', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Categoria', 'categoria', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Preço Venda', 'preco_venda', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Estoque', 'estoque_atual', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Status', 'status', $sort_col, $sort_dir) ?></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                <tr class="<?= ($p['estoque_atual'] <= $p['estoque_minimo']) ? 'table-warning' : '' ?>">
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['sku'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['categoria'] ?? '-') ?></td>
                    <td><?= formatarMoeda($p['preco_venda']) ?></td>
                    <td>
                        <?php if ($p['estoque_atual'] <= $p['estoque_minimo']): ?>
                            <span class="text-danger"><strong><?= $p['estoque_atual'] ?></strong></span>
                        <?php else: ?>
                            <?= $p['estoque_atual'] ?>
                        <?php endif; ?>
                        <?= $p['unidade'] ?>
                    </td>
                    <td>
                        <span class="badge badge-<?= $p['status'] == 'ativo' ? 'success' : 'danger' ?>">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?page=produtos&action=view&id=<?= $p['id'] ?>" class="btn btn-sm btn-info" title="Visualizar">👁️</a>
                        <a href="?page=produtos&action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                        <a href="?page=produtos&action=movimentacao&id=<?= $p['id'] ?>" class="btn btn-sm btn-secondary" title="Movimentação de Estoque">📦</a>
                        <a href="?page=produtos&action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?')">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
