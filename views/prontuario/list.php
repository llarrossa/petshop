<?php
$page_title = 'Prontuário';
$sort_col   = $_GET['orderby'] ?? 'data_atendimento';
$sort_dir   = $_GET['order']   ?? 'desc';
ob_start();
?>

<div class="page-header">
    <h2>📋 Prontuário</h2>
    <a href="?page=prontuario&action=create" class="btn btn-primary">+ Novo Registro</a>
</div>

<!-- Filtros -->
<div class="card">
    <div class="card-header">
        <h3>Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="form-inline">
            <input type="hidden" name="page"   value="prontuario">
            <input type="hidden" name="action" value="list">

            <select name="pet_id" class="form-control">
                <option value="">Todos os pets</option>
                <?php foreach ($pets as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= (($_GET['pet_id'] ?? '') == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                        <?php if (!empty($p['tutor_nome'])): ?>
                            (<?= htmlspecialchars($p['tutor_nome']) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="cliente_id" class="form-control">
                <option value="">Todos os clientes</option>
                <?php foreach ($tutores as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= (($_GET['cliente_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="data_inicio" class="form-control"
                   value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>"
                   title="Data inicial">

            <input type="date" name="data_fim" class="form-control"
                   value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>"
                   title="Data final">

            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=prontuario&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<!-- Tabela -->
<div class="card">
    <div class="card-body">
        <?php if (empty($prontuarios)): ?>
            <p class="text-center text-muted">Nenhum registro de prontuário encontrado.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= thSort('Data', 'data_atendimento', $sort_col, $sort_dir) ?></th>
                        <th><?= thSort('Pet',  'pet_nome',         $sort_col, $sort_dir) ?></th>
                        <th><?= thSort('Cliente', 'cliente_nome',  $sort_col, $sort_dir) ?></th>
                        <th>Profissional</th>
                        <th>Peso</th>
                        <th>Observações</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prontuarios as $reg): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($reg['data_atendimento'])) ?></td>
                        <td>
                            <a href="?page=pets&action=view&id=<?= $reg['pet_id'] ?>">
                                <?= htmlspecialchars($reg['pet_nome'] ?? '-') ?>
                            </a>
                            <?php if (!empty($reg['pet_especie'])): ?>
                                <small class="text-muted">(<?= htmlspecialchars(ucfirst($reg['pet_especie'])) ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?page=clientes&action=view&id=<?= $reg['cliente_id'] ?>">
                                <?= htmlspecialchars($reg['cliente_nome'] ?? '-') ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($reg['profissional_nome'] ?? '-') ?></td>
                        <td><?= $reg['peso'] ? number_format($reg['peso'], 2, ',', '.') . ' kg' : '-' ?></td>
                        <td class="text-muted" style="max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?= htmlspecialchars($reg['observacoes'] ?? '-') ?>
                        </td>
                        <td class="actions">
                            <a href="?page=prontuario&action=edit&id=<?= $reg['id'] ?>"
                               class="btn btn-sm btn-warning" title="Editar">✏️</a>
                            <a href="?page=prontuario&action=delete&id=<?= $reg['id'] ?>"
                               class="btn btn-sm btn-danger" title="Excluir"
                               onclick="return confirm('Excluir este registro de prontuário?')">🗑️</a>
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
            Exibindo <?= count($prontuarios) ?> de <?= $total ?> registro<?= $total !== 1 ? 's' : '' ?>
        </p>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
