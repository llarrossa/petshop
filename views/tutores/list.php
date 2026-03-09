<?php
$page_title = 'Lista de Tutores';
$sort_col = $_GET['orderby'] ?? 'id';
$sort_dir = $_GET['order'] ?? 'asc';
ob_start();
?>

<div class="page-header">
    <h2>Tutores / Clientes</h2>
    <a href="?page=tutores&action=create" class="btn btn-primary">+ Novo Tutor</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="form-inline">
            <input type="hidden" name="action" value="list">
            <input type="text" name="nome" placeholder="Buscar por nome..." value="<?= $_GET['nome'] ?? '' ?>" class="form-control">
            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <option value="ativo" <?= (isset($_GET['status']) && $_GET['status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= (isset($_GET['status']) && $_GET['status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=tutores&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($tutores)): ?>
            <p class="text-center">Nenhum tutor cadastrado.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?= thSort('ID', 'id', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Nome', 'nome', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Telefone', 'telefone', $sort_col, $sort_dir) ?></th>
                    <th>WhatsApp</th>
                    <th><?= thSort('Email', 'email', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Status', 'status', $sort_col, $sort_dir) ?></th>
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
                        <span class="badge badge-<?= $t['status'] == 'ativo' ? 'success' : 'danger' ?>">
                            <?= ucfirst($t['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?page=tutores&action=view&id=<?= $t['id'] ?>" class="btn btn-sm btn-info" title="Visualizar">👁️</a>
                        <a href="?page=tutores&action=edit&id=<?= $t['id'] ?>" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                        <a href="?page=tutores&action=delete&id=<?= $t['id'] ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este tutor?')">🗑️</a>
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
