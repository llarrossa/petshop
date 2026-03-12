<?php
$page_title = 'Agenda';
$sort_col   = $_GET['orderby'] ?? 'data';
$sort_dir   = $_GET['order']   ?? 'desc';

$qs_filtros = http_build_query(array_filter([
    'page'            => 'agenda',
    'action'          => 'list',
    'data'            => $_GET['data']            ?? '',
    'status'          => $_GET['status']          ?? '',
    'profissional_id' => $_GET['profissional_id'] ?? '',
    'busca'           => $_GET['busca']           ?? '',
    'orderby'         => $sort_col,
    'order'           => $sort_dir,
], fn($v) => $v !== ''));

$status_badges = [
    'agendado'       => 'info',
    'confirmado'     => 'primary',
    'em_atendimento' => 'warning',
    'finalizado'     => 'success',
    'cancelado'      => 'danger',
    'faltou'         => 'secondary',
];
ob_start();
?>

<div class="page-header">
    <h2>Agenda</h2>
    <a href="?page=agenda&action=create" class="btn btn-primary">+ Novo Agendamento</a>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page"   value="agenda">
            <input type="hidden" name="action" value="list">
            <input type="text" name="busca" placeholder="Buscar por pet ou cliente..."
                   value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" class="form-control">
            <input type="date" name="data" value="<?= htmlspecialchars($_GET['data'] ?? '') ?>" class="form-control">
            <select name="profissional_id" class="form-control">
                <option value="">Todos os profissionais</option>
                <?php foreach ($profissionais as $pr): ?>
                <option value="<?= $pr['id'] ?>" <?= (isset($_GET['profissional_id']) && $_GET['profissional_id'] == $pr['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pr['nome']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <?php foreach ($status_badges as $s => $b): ?>
                <option value="<?= $s ?>" <?= (isset($_GET['status']) && $_GET['status'] == $s) ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="?page=agenda&action=list" class="btn btn-link">Limpar</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($agendamentos)): ?>
            <p class="text-center">Nenhum agendamento encontrado.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?= thSort('Data',         'data',              $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Hora',         'hora',              $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Pet',          'pet_nome',          $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Cliente',      'tutor_nome',        $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Serviço',      'servico_nome',      $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Profissional', 'profissional_nome', $sort_col, $sort_dir) ?></th>
                    <th><?= thSort('Status',       'status',            $sort_col, $sort_dir) ?></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agendamentos as $ag): ?>
                <tr>
                    <td><?= formatarData($ag['data']) ?></td>
                    <td><?= substr($ag['hora'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($ag['pet_nome']) ?></td>
                    <td><?= htmlspecialchars($ag['tutor_nome']) ?></td>
                    <td><?= htmlspecialchars($ag['servico_nome']) ?></td>
                    <td><?= htmlspecialchars($ag['profissional_nome'] ?? '-') ?></td>
                    <td>
                        <span class="badge badge-<?= $status_badges[$ag['status']] ?? 'secondary' ?>">
                            <?= ucfirst(str_replace('_', ' ', $ag['status'])) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?page=agenda&action=edit&id=<?= $ag['id'] ?>" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                        <?php if (!in_array($ag['status'], ['finalizado', 'cancelado'])): ?>
                        <a href="?page=agenda&action=status&id=<?= $ag['id'] ?>&novo_status=finalizado" class="btn btn-sm btn-success" title="Finalizar" onclick="return confirm('Finalizar atendimento?')">✅</a>
                        <a href="?page=agenda&action=status&id=<?= $ag['id'] ?>&novo_status=cancelado" class="btn btn-sm btn-danger" title="Cancelar" onclick="return confirm('Cancelar agendamento?')">✖️</a>
                        <?php endif; ?>
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
            Exibindo <?= count($agendamentos) ?> de <?= $total ?> agendamento<?= $total !== 1 ? 's' : '' ?>
        </p>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
