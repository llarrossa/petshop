<?php
$page_title = 'Agenda';
ob_start();
$status_badges = [
    'agendado' => 'info',
    'confirmado' => 'primary',
    'em_atendimento' => 'warning',
    'finalizado' => 'success',
    'cancelado' => 'danger',
    'faltou' => 'secondary',
];
?>

<div class="page-header">
    <h2>Agenda</h2>
    <a href="?page=agenda&action=create" class="btn btn-primary">+ Novo Agendamento</a>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page" value="agenda">
            <input type="hidden" name="action" value="list">
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
                    <th>Data</th><th>Hora</th><th>Pet</th><th>Tutor</th>
                    <th>Serviço</th><th>Profissional</th><th>Status</th><th>Ações</th>
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
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
