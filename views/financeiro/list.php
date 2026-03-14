<?php
$page_title = 'Financeiro';
ob_start();
?>

<div class="page-header">
    <h2>Financeiro</h2>
    <a href="?page=financeiro&action=create" class="btn btn-primary">+ Novo Lançamento</a>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page" value="financeiro">
            <input type="hidden" name="action" value="list">
            <label>De: <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>" class="form-control"></label>
            <label>Até: <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>" class="form-control"></label>
            <select name="tipo" class="form-control">
                <option value="">Todos os tipos</option>
                <option value="receita" <?= ($tipo == 'receita') ? 'selected' : '' ?>>Receita</option>
                <option value="despesa" <?= ($tipo == 'despesa') ? 'selected' : '' ?>>Despesa</option>
            </select>
            <select name="status" class="form-control">
                <option value="">Todos os status</option>
                <option value="pago" <?= ($status == 'pago') ? 'selected' : '' ?>>Pago</option>
                <option value="pendente" <?= ($status == 'pendente') ? 'selected' : '' ?>>Pendente</option>
                <option value="cancelado" <?= ($status == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
        </form>
    </div>
</div>

<div class="dashboard-cards" style="margin-bottom:1rem;">
    <div class="card card-success">
        <div class="card-icon">📈</div>
        <div class="card-content">
            <h3>Receitas</h3>
            <p class="value"><?= formatarMoeda($totais['total_receitas'] ?? 0) ?></p>
        </div>
    </div>
    <div class="card card-danger" style="background:#dc3545; color:#fff;">
        <div class="card-icon">📉</div>
        <div class="card-content" style="color:#fff;">
            <h3 style="color:#fff;">Despesas</h3>
            <p class="value"><?= formatarMoeda($totais['total_despesas'] ?? 0) ?></p>
        </div>
    </div>
    <div class="card card-primary">
        <div class="card-icon">💰</div>
        <div class="card-content">
            <h3>Saldo</h3>
            <p class="value"><?= formatarMoeda(($totais['total_receitas'] ?? 0) - ($totais['total_despesas'] ?? 0)) ?></p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($lancamentos)): ?>
            <p class="text-center">Nenhum lançamento no período.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr><th>Data</th><th>Tipo</th><th>Categoria</th><th>Descrição</th><th>Valor</th><th>Forma Pgto</th><th>Status</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($lancamentos as $l): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($l['data_pagamento'])) ?></td>
                    <td><span class="badge badge-<?= $l['tipo'] == 'receita' ? 'success' : 'danger' ?>"><?= ucfirst($l['tipo']) ?></span></td>
                    <td><?= htmlspecialchars($l['categoria'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($l['descricao']) ?></td>
                    <td><?= formatarMoeda($l['valor']) ?></td>
                    <td><?= htmlspecialchars($l['forma_pagamento'] ?? '-') ?></td>
                    <td><span class="badge badge-<?= $l['status'] == 'pago' ? 'success' : ($l['status'] == 'pendente' ? 'warning' : 'secondary') ?>"><?= ucfirst($l['status']) ?></span></td>
                    <td>
                        <?php if (empty($l['venda_id'])): ?>
                        <a href="?page=financeiro&action=delete&id=<?= $l['id'] ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Excluir este lançamento?')">🗑️</a>
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
