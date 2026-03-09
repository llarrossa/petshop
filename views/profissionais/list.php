<?php
$page_title = 'Profissionais';
ob_start();
?>

<div class="page-header">
    <h2>Profissionais</h2>
    <a href="?page=profissionais&action=create" class="btn btn-primary">+ Novo Profissional</a>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page" value="profissionais">
            <input type="hidden" name="action" value="list">
            <input type="text" name="nome" placeholder="Buscar por nome..." value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" class="form-control">
            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <option value="ativo" <?= (isset($_GET['status']) && $_GET['status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= (isset($_GET['status']) && $_GET['status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=profissionais&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($profissionais)): ?>
            <p class="text-center">Nenhum profissional cadastrado.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th><th>Nome</th><th>Função</th><th>Telefone</th><th>E-mail</th>
                    <th>Comissão</th><th>Status</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profissionais as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['funcao'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['telefone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['email'] ?? '-') ?></td>
                    <td>
                        <?php if ($p['comissao'] > 0): ?>
                            <?= $p['comissao'] ?><?= $p['tipo_comissao'] == 'percentual' ? '%' : ' R$/serviço' ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-<?= $p['status'] == 'ativo' ? 'success' : 'danger' ?>">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?page=profissionais&action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                        <a href="?page=profissionais&action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este profissional?')">🗑️</a>
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
