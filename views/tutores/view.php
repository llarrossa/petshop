<?php
$page_title = 'Detalhes do Cliente';
ob_start();
?>

<div class="page-header">
    <h2><?= htmlspecialchars($dados['nome']) ?></h2>
    <div>
        <a href="?page=clientes&action=edit&id=<?= $dados['id'] ?>" class="btn btn-warning">✏️ Editar</a>
        <a href="?page=clientes&action=list" class="btn btn-secondary">← Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Dados do Cliente</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Nome</label>
                <p><?= htmlspecialchars($dados['nome']) ?></p>
            </div>
            <div class="form-group col-md-3">
                <label>CPF</label>
                <p><?= htmlspecialchars($dados['cpf'] ?? '-') ?></p>
            </div>
            <div class="form-group col-md-3">
                <label>Status</label>
                <p><span class="badge badge-<?= $dados['status'] == 'ativo' ? 'success' : 'danger' ?>"><?= ucfirst($dados['status']) ?></span></p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Telefone</label>
                <p><?= htmlspecialchars($dados['telefone'] ?? '-') ?></p>
            </div>
            <div class="form-group col-md-4">
                <label>WhatsApp</label>
                <p><?= htmlspecialchars($dados['whatsapp'] ?? '-') ?></p>
            </div>
            <div class="form-group col-md-4">
                <label>E-mail</label>
                <p><?= htmlspecialchars($dados['email'] ?? '-') ?></p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Endereço</label>
                <p><?= htmlspecialchars($dados['endereco'] ?? '-') ?></p>
            </div>
            <div class="form-group col-md-4">
                <label>Cidade / Estado</label>
                <p><?= htmlspecialchars($dados['cidade'] ?? '') ?><?= !empty($dados['estado']) ? ' / ' . $dados['estado'] : '' ?></p>
            </div>
            <div class="form-group col-md-2">
                <label>CEP</label>
                <p><?= htmlspecialchars($dados['cep'] ?? '-') ?></p>
            </div>
        </div>
        <?php if (!empty($dados['observacoes'])): ?>
        <div class="form-group">
            <label>Observações</label>
            <p><?= nl2br(htmlspecialchars($dados['observacoes'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>🐕 Pets</h3>
        <a href="?page=pets&action=create" class="btn btn-sm btn-primary">+ Novo Pet</a>
    </div>
    <div class="card-body">
        <?php if (empty($pets)): ?>
            <p class="text-muted text-center">Nenhum pet cadastrado.</p>
        <?php else: ?>
        <table class="table table-sm">
            <thead>
                <tr><th>Nome</th><th>Espécie</th><th>Raça</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($pets as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['especie'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['raca'] ?? '-') ?></td>
                    <td><span class="badge badge-<?= $p['status'] == 'ativo' ? 'success' : 'danger' ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td><a href="?page=pets&action=view&id=<?= $p['id'] ?>" class="btn btn-sm btn-info">👁️</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>📅 Agendamentos</h3>
    </div>
    <div class="card-body">
        <?php if (empty($agendamentos)): ?>
            <p class="text-muted text-center">Nenhum agendamento encontrado.</p>
        <?php else: ?>
        <table class="table table-sm">
            <thead>
                <tr><th>Data</th><th>Hora</th><th>Pet</th><th>Serviço</th><th>Profissional</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($agendamentos, 0, 10) as $ag): ?>
                <tr>
                    <td><?= formatarData($ag['data']) ?></td>
                    <td><?= substr($ag['hora'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($ag['pet_nome']) ?></td>
                    <td><?= htmlspecialchars($ag['servico_nome']) ?></td>
                    <td><?= htmlspecialchars($ag['profissional_nome'] ?? '-') ?></td>
                    <td><span class="badge badge-<?= $ag['status'] ?>"><?= ucfirst($ag['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>💰 Histórico de Compras</h3>
    </div>
    <div class="card-body">
        <?php if (empty($historico_vendas)): ?>
            <p class="text-muted text-center">Nenhuma compra registrada.</p>
        <?php else: ?>
        <table class="table table-sm">
            <thead>
                <tr><th>#</th><th>Data</th><th>Pet</th><th>Valor</th><th>Forma Pgto</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($historico_vendas as $v): ?>
                <tr>
                    <td>#<?= $v['id'] ?></td>
                    <td><?= formatarData(date('Y-m-d', strtotime($v['data']))) ?></td>
                    <td><?= htmlspecialchars($v['pet_nome'] ?? '-') ?></td>
                    <td><?= formatarMoeda($v['valor_final']) ?></td>
                    <td><?= ucfirst($v['forma_pagamento']) ?></td>
                    <td><span class="badge badge-<?= $v['status'] == 'finalizada' ? 'success' : 'danger' ?>"><?= ucfirst($v['status']) ?></span></td>
                    <td><a href="?page=vendas&action=view&id=<?= $v['id'] ?>" class="btn btn-sm btn-info">👁️</a></td>
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
