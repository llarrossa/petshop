<?php
$page_title = 'Pets';
$sort_col = $_GET['orderby'] ?? 'id';
$sort_dir = $_GET['order'] ?? 'asc';
ob_start();
?>

<div class="page-header">
    <h1>🐕 Pets</h1>
    <a href="?page=pets&action=create" class="btn btn-primary">+ Novo Pet</a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="filters-form">
            <input type="hidden" name="page" value="pets">
            <input type="hidden" name="action" value="list">

            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou raça..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="tutor_id" class="form-control">
                        <option value="">Todos os clientes</option>
                        <?php foreach ($tutores as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= (isset($_GET['tutor_id']) && $_GET['tutor_id'] == $t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="especie" class="form-control">
                        <option value="">Todas as espécies</option>
                        <option value="cachorro" <?= (isset($_GET['especie']) && $_GET['especie'] == 'cachorro') ? 'selected' : '' ?>>Cachorro</option>
                        <option value="gato" <?= (isset($_GET['especie']) && $_GET['especie'] == 'gato') ? 'selected' : '' ?>>Gato</option>
                        <option value="passaro" <?= (isset($_GET['especie']) && $_GET['especie'] == 'passaro') ? 'selected' : '' ?>>Pássaro</option>
                        <option value="roedor" <?= (isset($_GET['especie']) && $_GET['especie'] == 'roedor') ? 'selected' : '' ?>>Roedor</option>
                        <option value="reptil" <?= (isset($_GET['especie']) && $_GET['especie'] == 'reptil') ? 'selected' : '' ?>>Réptil</option>
                        <option value="outro" <?= (isset($_GET['especie']) && $_GET['especie'] == 'outro') ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Listagem -->
<div class="card">
    <div class="card-body">
        <?php if (empty($pets)): ?>
            <div class="empty-state">
                <p>Nenhum pet cadastrado.</p>
                <a href="?page=pets&action=create" class="btn btn-primary">Cadastrar Primeiro Pet</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= thSort('Nome', 'nome', $sort_col, $sort_dir) ?></th>
                            <th><?= thSort('Espécie', 'especie', $sort_col, $sort_dir) ?></th>
                            <th><?= thSort('Raça', 'raca', $sort_col, $sort_dir) ?></th>
                            <th><?= thSort('Sexo', 'sexo', $sort_col, $sort_dir) ?></th>
                            <th><?= thSort('Cliente', 'tutor_nome', $sort_col, $sort_dir) ?></th>
                            <th>Telefone</th>
                            <th>Idade</th>
                            <th><?= thSort('Status', 'status', $sort_col, $sort_dir) ?></th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pets as $p): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['nome']) ?></strong></td>
                                <td><?= htmlspecialchars(ucfirst($p['especie'])) ?></td>
                                <td><?= htmlspecialchars($p['raca'] ?: '-') ?></td>
                                <td><?= $p['sexo'] == 'macho' ? 'Macho' : ($p['sexo'] == 'femea' ? 'Fêmea' : '-') ?></td>
                                <td><?= htmlspecialchars($p['tutor_nome']) ?></td>
                                <td><?= htmlspecialchars($p['tutor_telefone'] ?: '-') ?></td>
                                <td>
                                    <?php
                                    if ($p['data_nascimento']) {
                                        $nascimento = new DateTime($p['data_nascimento']);
                                        $hoje = new DateTime();
                                        $idade = $hoje->diff($nascimento);

                                        if ($idade->y > 0) {
                                            echo $idade->y . ' ano' . ($idade->y > 1 ? 's' : '');
                                        } elseif ($idade->m > 0) {
                                            echo $idade->m . ' ' . ($idade->m > 1 ? 'meses' : 'mês');
                                        } else {
                                            echo $idade->d . ' dia' . ($idade->d > 1 ? 's' : '');
                                        }
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $p['status'] == 'ativo' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?page=pets&action=view&id=<?= $p['id'] ?>" class="btn btn-sm btn-info" title="Visualizar">
                                            👁️
                                        </a>
                                        <a href="?page=pets&action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                            ✏️
                                        </a>
                                        <a href="?page=pets&action=delete&id=<?= $p['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tem certeza que deseja remover este pet?')"
                                           title="Excluir">
                                            🗑️
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
