<?php
$page_title = 'Pets';
$sort_col   = $_GET['orderby'] ?? 'id';
$sort_dir   = $_GET['order']   ?? 'asc';

$qs_filtros = http_build_query(array_filter([
    'page'     => 'pets',
    'action'   => 'list',
    'busca'    => $_GET['busca']    ?? '',
    'tutor_id' => $_GET['tutor_id'] ?? '',
    'especie'  => $_GET['especie']  ?? '',
    'orderby'  => $sort_col,
    'order'    => $sort_dir,
], fn($v) => $v !== ''));

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
            <input type="hidden" name="page"   value="pets">
            <input type="hidden" name="action" value="list">

            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="busca" class="form-control"
                           placeholder="Buscar por nome ou raça..."
                           value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="tutor_id" class="form-control">
                        <option value="">Todos os clientes</option>
                        <?php foreach ($tutores as $t): ?>
                            <option value="<?= $t['id'] ?>"
                                <?= (isset($_GET['tutor_id']) && $_GET['tutor_id'] == $t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="especie" class="form-control">
                        <option value="">Todas as espécies</option>
                        <option value="cachorro" <?= (($_GET['especie'] ?? '') === 'cachorro') ? 'selected' : '' ?>>Cachorro</option>
                        <option value="gato"     <?= (($_GET['especie'] ?? '') === 'gato')     ? 'selected' : '' ?>>Gato</option>
                        <option value="passaro"  <?= (($_GET['especie'] ?? '') === 'passaro')  ? 'selected' : '' ?>>Pássaro</option>
                        <option value="roedor"   <?= (($_GET['especie'] ?? '') === 'roedor')   ? 'selected' : '' ?>>Roedor</option>
                        <option value="reptil"   <?= (($_GET['especie'] ?? '') === 'reptil')   ? 'selected' : '' ?>>Réptil</option>
                        <option value="outro"    <?= (($_GET['especie'] ?? '') === 'outro')    ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
                <div class="col-md-2" style="display:flex;gap:6px;">
                    <button type="submit" class="btn btn-secondary btn-block">Filtrar</button>
                    <a href="?page=pets&action=list" class="btn btn-link">Limpar</a>
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
                <p>Nenhum pet encontrado.</p>
                <a href="?page=pets&action=create" class="btn btn-primary">Cadastrar Primeiro Pet</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= thSort('Nome',     'nome',       $sort_col, $sort_dir) ?></th>
                            <th><?= thSort('Espécie',  'especie',    $sort_col, $sort_dir) ?></th>
                            <th><?= thSort('Raça',     'raca',       $sort_col, $sort_dir) ?></th>
                            <th><?= thSort('Sexo',     'sexo',       $sort_col, $sort_dir) ?></th>
                            <th><?= thSort('Cliente',  'tutor_nome', $sort_col, $sort_dir) ?></th>
                            <th>Telefone</th>
                            <th>Idade</th>
                            <th><?= thSort('Status',   'status',     $sort_col, $sort_dir) ?></th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pets as $p): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['nome']) ?></strong></td>
                                <td><?= htmlspecialchars(ucfirst($p['especie'])) ?></td>
                                <td><?= htmlspecialchars($p['raca'] ?: '-') ?></td>
                                <td><?= $p['sexo'] === 'macho' ? 'Macho' : ($p['sexo'] === 'femea' ? 'Fêmea' : '-') ?></td>
                                <td><?= htmlspecialchars($p['tutor_nome']) ?></td>
                                <td><?= htmlspecialchars($p['tutor_telefone'] ?: '-') ?></td>
                                <td>
                                    <?php
                                    if ($p['data_nascimento']) {
                                        $nasc  = new DateTime($p['data_nascimento']);
                                        $idade = (new DateTime())->diff($nasc);
                                        if ($idade->y > 0)      echo $idade->y . ' ano' . ($idade->y > 1 ? 's' : '');
                                        elseif ($idade->m > 0)  echo $idade->m . ' ' . ($idade->m > 1 ? 'meses' : 'mês');
                                        else                    echo $idade->d . ' dia' . ($idade->d > 1 ? 's' : '');
                                    } else { echo '-'; }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $p['status'] === 'ativo' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?page=pets&action=view&id=<?= $p['id'] ?>"   class="btn btn-sm btn-info"    title="Visualizar">👁️</a>
                                        <a href="?page=pets&action=edit&id=<?= $p['id'] ?>"   class="btn btn-sm btn-warning" title="Editar">✏️</a>
                                        <a href="?page=pets&action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tem certeza que deseja remover este pet?')" title="Excluir">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

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
                Exibindo <?= count($pets) ?> de <?= $total ?> pet<?= $total !== 1 ? 's' : '' ?>
            </p>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
