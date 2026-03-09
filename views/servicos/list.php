<?php
$page_title = 'Serviços';
$sort_col = $_GET['orderby'] ?? 'id';
$sort_dir = $_GET['order'] ?? 'asc';
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
            <input type="hidden" name="page" value="servicos">
            <input type="hidden" name="action" value="list">
            <input type="text" name="nome" placeholder="Buscar por nome..." value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" class="form-control">
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
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=servicos&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($servicos)): ?>
            <p class="text-center">Nenhum serviço cadastrado.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?= thSort('ID', 'id', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Nome', 'nome', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Categoria', 'categoria', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Preço', 'preco', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Duração (min)', 'duracao_media', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Status', 'status', $sort_col, $sort_dir) ?></th>
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
                        <span class="badge badge-<?= $s['status'] == 'ativo' ? 'success' : 'danger' ?>">
                            <?= ucfirst($s['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?page=servicos&action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                        <a href="?page=servicos&action=delete&id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este serviço?')">🗑️</a>
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
